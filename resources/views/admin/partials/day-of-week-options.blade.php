@php
    $day = $selected ?? old('day_of_week', 1);
@endphp
@foreach ([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'] as $val => $label)
    <option value="{{ $val }}" @selected((int) $day === $val)>{{ $label }}</option>
@endforeach
