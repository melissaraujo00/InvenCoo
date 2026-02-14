@props(['headers'])

<thead>
    <tr class="border-gray-200 border-y dark:border-gray-700">
        @foreach($headers as $header)
            <th class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                {{ $header }}
            </th>
        @endforeach
        <th class="px-4 py-3 font-normal text-right text-gray-500 text-theme-sm dark:text-gray-400">
            Acciones
        </th>
    </tr>
</thead>
