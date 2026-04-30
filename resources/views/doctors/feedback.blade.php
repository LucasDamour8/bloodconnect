<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-comments text-pink-600"></i>
            {{ __('Donor Feedback & Inquiries') }}
        </div>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800">Recent Messages</h3>
            <p class="text-sm text-gray-500">View and manage communications from donors.</p>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($feedbacks ?? [] as $item)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                {{-- Icon placeholder since DB table might not have user object --}}
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $item->email }}</h4>
                                <p class="text-xs text-gray-400">
                                    {{-- Use Carbon to parse the string date manually --}}
                                    {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ ($item->status ?? '') === 'read' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $item->status ?? 'New' }}
                        </span>
                    </div>
                    
                    <div class="ml-13">
                        <p class="text-gray-600 text-sm leading-relaxed italic">
                            "{{ $item->message }}"
                        </p>
                    </div>

                    <div class="mt-4 ml-13 flex gap-2">
                        <a href="mailto:{{ $item->email }}" class="text-xs font-bold text-indigo-600 hover:underline">
                            <i class="fa-solid fa-reply mr-1"></i> Reply via Email
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-20 text-center">
                    <i class="fa-solid fa-comment-slash text-4xl text-gray-200 mb-4"></i>
                    <p class="text-gray-400 font-medium">No feedback messages found in userfeedbacks.</p>
                </div>
            @endforelse
        </div>
        
        @if(method_exists($feedbacks, 'links'))
            <div class="p-4 border-t border-gray-100">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>