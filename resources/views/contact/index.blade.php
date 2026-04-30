<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Reach Support</h2>
                    <p class="text-gray-500 text-sm">Send us your issues or feedback. Our admins will reply privately.</p>
                </div>
                {{-- Optional: Back to Dashboard link --}}
                <a href="{{ route('dashboard') }}" class="text-[10px] font-black uppercase text-gray-400 hover:text-red-600 transition tracking-widest">
                    &larr; Back to Dashboard
                </a>
            </div>

            {{-- Success Alert --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-center gap-3 shadow-sm shadow-green-100">
                    <div class="bg-green-500 rounded-full p-1">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-green-700 uppercase tracking-tight">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Form to Send Feedback --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 mb-10">
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-1">Subject</label>
                        <input type="text" name="subject" required 
                               value="{{ old('subject') }}"
                               placeholder="e.g., Issue with appointment booking"
                               class="w-full mt-2 border-none bg-gray-50 rounded-2xl focus:ring-2 focus:ring-red-500 text-sm font-semibold p-4 transition-all">
                        @error('subject')
                            <p class="text-red-500 text-[10px] mt-1 font-bold uppercase tracking-tighter">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest ml-1">Your Message</label>
                        <textarea name="message" rows="4" required 
                                  placeholder="Describe your issue in detail..."
                                  class="w-full mt-2 border-none bg-gray-50 rounded-2xl focus:ring-2 focus:ring-red-500 text-sm font-semibold p-4 transition-all">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-[10px] mt-1 font-bold uppercase tracking-tighter">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="bg-red-600 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition shadow-lg shadow-red-100 transform active:scale-95">
                        Send Message
                    </button>
                </form>
            </div>

            {{-- Feedback History --}}
            <div class="space-y-6">
                <div class="flex items-center gap-4 px-2">
                    <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest whitespace-nowrap">Your Conversations</h3>
                    <div class="h-px bg-gray-100 w-full"></div>
                </div>
                
                @forelse($messages as $msg)
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden transition hover:shadow-md">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[9px] font-black text-red-600 uppercase tracking-tighter bg-red-50 px-2 py-0.5 rounded">#{{ $msg->id }}</span>
                                <h4 class="font-bold text-gray-900 mt-1">{{ $msg->subject }}</h4>
                            </div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $msg->created_at->format('M d, Y') }}</p>
                        </div>
                        <p class="text-sm text-gray-600 mt-4 leading-relaxed font-medium">{{ $msg->message }}</p>
                    </div>

                    @if($msg->admin_reply)
                    <div class="p-6 bg-green-50/40 relative">
                        {{-- Visual Indicator --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500"></div>
                        
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-[10px] font-black text-green-700 uppercase tracking-widest">Admin Reply</span>
                        </div>
                        <p class="text-sm text-green-900 font-semibold leading-relaxed">
                            {{ $msg->admin_reply }}
                        </p>
                        {{-- Reply Timestamp --}}
                        @if($msg->updated_at != $msg->created_at)
                             <p class="text-[9px] text-green-600/50 font-bold uppercase mt-3">Replied on {{ $msg->updated_at->format('M d, Y') }}</p>
                        @endif
                    </div>
                    @else
                    <div class="p-4 bg-gray-50/50 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="flex gap-1">
                                <span class="w-1 h-1 bg-gray-300 rounded-full animate-bounce"></span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                            </div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Awaiting Admin Response</p>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="bg-white rounded-3xl border border-dashed border-gray-200 py-16 text-center shadow-sm">
                    <div class="bg-gray-50 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">No conversation history yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>