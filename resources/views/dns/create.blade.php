<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white text-gray-800 leading-tight">
            Add DNS record to linode
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <form action="{{ route('dns.import') }}" method="POST">
                @csrf

                <div>
                    <label for="domains">Domain Names (one per line):</label>
                    <textarea style="border-radius: 5px; border: none;" name="domains" id="domains" rows="4" cols="150"></textarea>
                </div>

                <label for="server">Select Server:</label>
                <div>
                    <select style="border: none; border-radius: 4px;  width: 220px;" name="server" id="server">
                        @foreach ($servers as $server)
                            <option value="{{ $server->id }}">{{ $server->name }} </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class=" rounded-md  px-3 py-2 border border-transparent font-medium  bg-[color:var(--cdre-blue)] text-white btn btn-primary btn-sm mt-4">
                    Submit
                </button>

                @if (session('dns_success') && is_array(session('dns_success')))
                    <div class="alert alert-success bg-blue-100 rounded-md p-2">
                        <ul>
                            @foreach (session('dns_success') as $message)
                                <li>{!! $message !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


            </form>
        </div>
    </div>

</x-app-layout>
