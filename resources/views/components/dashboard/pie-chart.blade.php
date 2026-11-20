@props([
    'slices' => [],
])

@php
    $size = 224;
    $center = $size / 2;
    $radius = $center - 2;
    $gap = 2.0;

    $cursor = -90.0;
    $wedges = [];

    foreach ($slices as $slice) {
        $sweep = ($slice['value'] / 100) * 360;
        $start = $cursor + $gap / 2;
        $end = $cursor + $sweep - $gap / 2;
        $cursor += $sweep;

        $startRad = deg2rad($start);
        $endRad = deg2rad($end);
        $midRad = deg2rad(($start + $end) / 2);

        $x1 = $center + $radius * cos($startRad);
        $y1 = $center + $radius * sin($startRad);
        $x2 = $center + $radius * cos($endRad);
        $y2 = $center + $radius * sin($endRad);

        $wedges[] = [
            'd' => sprintf(
                'M %s %s L %.2f %.2f A %s %s 0 %d 1 %.2f %.2f Z',
                $center, $center, $x1, $y1, $radius, $radius, ($end - $start) > 180 ? 1 : 0, $x2, $y2
            ),
            'fill' => $slice['color'],
            'label' => $slice['label'],
            'value' => $slice['value'],
            'labelX' => $center + $radius * 0.62 * cos($midRad),
            'labelY' => $center + $radius * 0.62 * sin($midRad),
        ];
    }
@endphp

<svg
    viewBox="0 0 {{ $size }} {{ $size }}"
    class="mx-auto h-auto w-full max-w-[224px]"
    role="img"
    aria-label="Expense statistics pie chart"
>
    @foreach ($wedges as $wedge)
        <path d="{{ $wedge['d'] }}" fill="{{ $wedge['fill'] }}" />
    @endforeach

    @foreach ($wedges as $wedge)
        <text
            x="{{ $wedge['labelX'] }}"
            y="{{ $wedge['labelY'] - 2 }}"
            text-anchor="middle"
            fill="#ffffff"
            font-size="12"
            font-weight="600"
        >{{ $wedge['value'] }}%</text>
        <text
            x="{{ $wedge['labelX'] }}"
            y="{{ $wedge['labelY'] + 11 }}"
            text-anchor="middle"
            fill="#ffffff"
            font-size="9"
        >{{ $wedge['label'] }}</text>
    @endforeach
</svg>
