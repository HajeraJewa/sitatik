@props(['active'])

@php
  $classes = ($active ?? false)
    ? 'flex items-center py-2.5 px-4 rounded bg-slate-800 text-blue-400 transition duration-200'
    : 'flex items-center py-2.5 px-4 rounded text-gray-400 hover:bg-slate-800 hover:text-white transition duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
  {{ $slot }}
</a>