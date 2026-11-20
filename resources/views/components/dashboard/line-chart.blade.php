@props([
    'labels' => [],
    'values' => [],
])

@php
    $chartWidth = 660;
    $chartHeight = 260;
    $padLeft = 42;
    $padRight = 8;
    $padTop = 12;
    $padBottom = 32;

    $plotWidth = $chartWidth - $padLeft - $padRight;
    $plotHeight = $chartHeight - $padTop - $padBottom;

    $peak = max($values ?: [1]);

    $stepCount = 4;
    $rawStep = $peak / $stepCount;
    $magnitude = 10 ** floor(log10(max($rawStep, 1)));
    $step = max($magnitude, ceil($rawStep / $magnitude) * $magnitude);
    $axisMax = $step * $stepCount;

    $count = count($values);
    $stepX = $count > 1 ? $plotWidth / ($count - 1) : $plotWidth;

    $points = [];
    foreach ($values as $index => $value) {
        $points[] = [
            $padLeft + $index * $stepX,
            $padTop + $plotHeight - ($value / $axisMax) * $plotHeight,
        ];
    }

    // Catmull-Rom -> cubic Bezier for a smooth line.
    $line = '';
    if ($points !== []) {
        $line = sprintf('M %.2f %.2f', $points[0][0], $points[0][1]);

        for ($i = 0; $i < count($points) - 1; $i++) {
            $p0 = $points[$i - 1] ?? $points[$i];
            $p1 = $points[$i];
            $p2 = $points[$i + 1];
            $p3 = $points[$i + 2] ?? $p2;

            $line .= sprintf(
                ' C %.2f %.2f, %.2f %.2f, %.2f %.2f',
                $p1[0] + ($p2[0] - $p0[0]) / 6,
                $p1[1] + ($p2[1] - $p0[1]) / 6,
                $p2[0] - ($p3[0] - $p1[0]) / 6,
                $p2[1] - ($p3[1] - $p1[1]) / 6,
                $p2[0],
                $p2[1],
            );
        }
    }

    $baseline = $padTop + $plotHeight;
    $area = $points === [] ? '' : sprintf(
        '%s L %.2f %.2f L %.2f %.2f Z',
        $line, end($points)[0], $baseline, $points[0][0], $baseline
    );
@endphp

<svg
    viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}"
    class="h-auto w-full"
    role="img"
    aria-label="Balance history line chart"
>
    <defs>
        <linearGradient id="balance-history-fill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#1814f3" stop-opacity="0.16" />
            <stop offset="100%" stop-color="#1814f3" stop-opacity="0" />
        </linearGradient>
    </defs>

    @for ($tick = 0; $tick <= $stepCount; $tick++)
        @php $gridY = $baseline - ($tick / $stepCount) * $plotHeight; @endphp
        <text x="{{ $padLeft - 12 }}" y="{{ $gridY + 4 }}" text-anchor="end" fill="#718ebf" font-size="11">
            {{ number_format($tick * $step) }}
        </text>
    @endfor

    @foreach ($labels as $index => $label)
        <line
            x1="{{ $padLeft + $index * $stepX }}"
            y1="{{ $padTop }}"
            x2="{{ $padLeft + $index * $stepX }}"
            y2="{{ $baseline }}"
            stroke="#e6eff5"
            stroke-width="1"
            stroke-dasharray="4 6"
        />
        <text
            x="{{ $padLeft + $index * $stepX }}"
            y="{{ $chartHeight - 8 }}"
            text-anchor="middle"
            fill="#718ebf"
            font-size="11"
        >{{ $label }}</text>
    @endforeach

    @if ($area !== '')
        <path d="{{ $area }}" fill="url(#balance-history-fill)" />
        <path d="{{ $line }}" fill="none" stroke="#1814f3" stroke-width="2.5" stroke-linecap="round" />
    @endif
</svg>
