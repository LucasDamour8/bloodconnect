<div class="space-y-12">

    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">
            {{ __('learn.title') }}
        </h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            {{ __('learn.subtitle') }}
        </p>
    </div>

    {{-- Info Grid --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- Who can donate --}}
        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-xl font-bold text-red-600 mb-3">
                {{ __('learn.who_can_donate') }}
            </h2>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                @foreach(__('learn.can_donate') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Who should wait --}}
        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-xl font-bold text-yellow-600 mb-3">
                {{ __('learn.who_should_wait') }}
            </h2>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                @foreach(__('learn.wait') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Types --}}
        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-xl font-bold text-blue-600 mb-3">
                {{ __('learn.types') }}
            </h2>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                @foreach(__('learn.types_list') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Safety --}}
        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-xl font-bold text-green-600 mb-3">
                {{ __('learn.safety') }}
            </h2>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                @foreach(__('learn.safety_list') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Process --}}
    <div class="bg-gray-50 p-6 rounded-2xl border">
        <h2 class="text-xl font-bold mb-3">
            {{ __('learn.process') }}
        </h2>
        <ol class="list-decimal pl-5 text-gray-700 space-y-1">
            @foreach(__('learn.process_list') as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    </div>

    {{-- Tips --}}
    <div class="bg-white p-6 rounded-2xl shadow border">
        <h2 class="text-xl font-bold mb-3">
            {{ __('learn.tips') }}
        </h2>
        <ul class="list-disc pl-5 text-gray-700 space-y-1">
            @foreach(__('learn.tips_list') as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    </div>

    {{-- CTA --}}
    <div class="text-center bg-red-600 text-white p-8 rounded-2xl">
        <h2 class="text-2xl font-bold mb-2">
            {{ __('learn.cta_title') }}
        </h2>
        <p class="mb-4 text-red-100">
            {{ __('learn.cta_subtitle') }}
        </p>
        <a href="{{ route('login') }}"
           class="bg-white text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-gray-100 transition">
            {{ __('learn.cta_button') }}
        </a>
    </div>

</div>