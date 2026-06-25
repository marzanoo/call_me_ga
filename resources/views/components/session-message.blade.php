@if (session('error'))
    <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
        <p class="font-semibold mb-2">Terjadi kesalahan:</p>
        <ul class="list-disc pl-5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif
