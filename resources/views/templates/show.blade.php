<x-layout :title="$template->name">
    <h2>{{ $template->name }}</h2>
    <p>{{ $template->description }}</p>

    <table class="table">
        <tr><th>#</th><th>Kontrolpunkt</th><th>Kategori</th></tr>
        @foreach ($template->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->text }}</td>
                <td>{{ $item->category->name }}</td>
            </tr>
        @endforeach
    </table>
</x-layout>
