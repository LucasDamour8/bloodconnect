<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">User Feedback & Support</h2>
                    <p class="text-gray-500 text-sm font-medium">Manage and reply to donor inquiries.</p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-white px-4 py-2 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Pending</p>
                        <p class="text-xl font-black text-red-600">{{ $feedbacks->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Subject</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($feedbacks as $item)
                        <tr class="hover:bg-gray-50/30 transition">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $item->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-medium">{{ $item->user->email }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">{{ $item->subject }}</p>
                                <p class="text-[11px] text-gray-400 line-clamp-1">{{ $item->message }}</p>
                            </td>
                            <td class="px-6 py-4 text-[11px] text-gray-500 font-bold uppercase">
                                {{ $item->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->status == 'pending')
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black uppercase rounded-full">Pending</span>
                                @else
                                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[9px] font-black uppercase rounded-full">Replied</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openReplyModal({{ $item->id }}, '{{ addslashes($item->message) }}', '{{ $item->admin_reply }}')" 
                                        class="bg-gray-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition">
                                    {{ $item->admin_reply ? 'View/Edit Reply' : 'Reply' }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-bold uppercase text-xs tracking-widest">No feedback records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $feedbacks->links() }}
            </div>
        </div>
    </div>

    <div id="replyModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl overflow-hidden transform transition-all">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Feedback Response</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-2xl">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">User Message</p>
                    <p id="userMessage" class="text-sm text-gray-600 italic font-medium"></p>
                </div>

                <form id="replyForm" method="POST">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Admin Response</label>
                        <textarea name="admin_reply" id="adminReplyText" rows="5" required 
                                  class="w-full border-none bg-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 text-sm font-semibold p-4"
                                  placeholder="Type your reply here..."></textarea>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="submit" class="flex-1 bg-red-600 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition">
                            Submit Response
                        </button>
                        <button type="button" onclick="closeModal()" class="px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openReplyModal(id, message, existingReply) {
            const modal = document.getElementById('replyModal');
            const form = document.getElementById('replyForm');
            const msgContainer = document.getElementById('userMessage');
            const textArea = document.getElementById('adminReplyText');

            form.action = `/admin/feedback/${id}/reply`;
            msgContainer.innerText = message;
            textArea.value = existingReply || '';
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }
    </script>
</x-app-layout>