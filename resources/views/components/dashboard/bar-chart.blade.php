@props([
    'labels' => [],
    'series' => [],
])

@php
    $chartWidth = 720;
    $chartHeight = 300;
    $padLeft = 46;
    $padRight = 6;
    $padTop = 10;
    $padBottom = 36;

    $plotWidth = $chartWidth - $padLeft - $padRight;
    $plotHeight = $chartHeight - $padTop - $padBottom;

    $peak = max(collect($series)->flatMap(fn ($serie) => $serie['values'])->push(1)->all());

    // Round the axis up to a clean step (e.g. 490 -> 500) so labels stay readable.
    $stepCount = 5;
    $rawStep = $peak / $stepCount;
    $magnitude = 10 ** floor(log10(max($rawStep, 1)));
    $step = max($magnitude, ceil($rawStep / $magnitude) * $magnitude);

    $groupCount = max(count($labels), 1);
    $groupWidth = $plotWidth / $groupCount;

    $barWidth = 13;
    $barGap = 9;
    $seriesCount = max(count($series), 1);
    $pairWidth = $barWidth * $seriesCount + $barGap * ($seriesCount - 1);

    $baseline = $padTop + $plotHeight;
@endphp

<svg
    viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}"
    class="h-auto w-full"
    role="img"
    aria-label="Weekly activity bar chart"
>
    @for ($tick = 0; $tick <= $stepCount; $tick++)
        @php $gridY = $baseline - ($tick / $stepCount) * $plotHeight; @endphp
        <line
            x1="{{ $padLeft }}"
            y1="{{ $gridY }}"
            x2="{{ $chartWidth - $padRight }}"
            y2="{{ $gridY }}"
            stroke="#f2f4f7"
            stroke-width="1"
        />
        <text x="{{ $padLeft - 14 }}" y="{{ $gridY + 4 }}" text-anchor="end" fill="#7c7692" font-size="11">
            {{ number_format($tick * $step) }}
        </text>
    @endfor

    @foreach ($labels as $index => $label)
        @php
            $groupX = $padLeft + $index * $groupWidth;
            $pairX = $groupX + ($groupWidth - $pairWidth) / 2;
        @endphp

        @foreach ($series as $serieIndex => $serie)
            @php
                $value = $serie['values'][$index] ?? 0;
                $barHeight = max(($value / ($step * $stepCount)) * $plotHeight, 2);
                $barX = $pairX + $serieIndex * ($barWidth + $barGap);
                $barY = $baseline - $barHeight;
            @endphp
            <rect
                x="{{ $barX }}"
                y="{{ $barY }}"
                width="{{ $barWidth }}"
                height="{{ $barHeight }}"
                rx="{{ $barWidth / 2 }}"
                fill="{{ $serie['color'] }}"
            />
        @endforeach

        <text
            x="{{ $groupX + $groupWidth / 2 }}"
            y="{{ $chartHeight - 10 }}"
            text-anchor="middle"
            fill="#7c7692"
            font-size="11"
        >{{ $label }}</text>
    @endforeach
</svg>
