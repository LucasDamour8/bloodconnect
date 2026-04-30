<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-lg p-8"
         x-data="donationWizard()">

        <h1 class="text-3xl font-black text-red-600 text-center mb-2">
            {{ __('donate.title') }}
        </h1>

        <p class="text-center text-gray-500 mb-6">
            {{ __('donate.subtitle') }}
        </p>

        {{-- STEP INDICATOR --}}
        <div class="flex justify-center mb-6 gap-2">
            <template x-for="(q, index) in questions" :key="index">
                <div class="w-3 h-3 rounded-full transition"
                     :class="index === currentStep ? 'bg-red-600 scale-125' : (answers[index] !== null ? 'bg-green-500' : 'bg-gray-300')">
                </div>
            </template>
        </div>

        {{-- QUESTIONS --}}
        <div class="text-center space-y-6">

            <template x-if="!finished">

                <div>

                    {{-- QUESTION --}}
                    <h2 class="text-xl font-bold text-gray-800 mb-4"
                        x-text="questions[currentStep].question"></h2>

                    {{-- YES / NO BUTTONS --}}
                    <div class="flex justify-center gap-4">

                        {{-- YES --}}
                        <button
                            @click="answers[currentStep] = true"
                            :class="answers[currentStep] === true 
                                ? 'bg-green-600 text-white ring-4 ring-green-200'
                                : 'bg-gray-100 text-gray-700 hover:bg-green-50'"
                            class="px-6 py-3 rounded-xl font-bold transition">
                            {{ __('donate.yes') }}
                        </button>

                        {{-- NO --}}
                        <button
                            @click="answers[currentStep] = false"
                            :class="answers[currentStep] === false 
                                ? 'bg-red-600 text-white ring-4 ring-red-200'
                                : 'bg-gray-100 text-gray-700 hover:bg-red-50'"
                            class="px-6 py-3 rounded-xl font-bold transition">
                            {{ __('donate.no') }}
                        </button>

                    </div>

                    {{-- NEXT BUTTON --}}
                    <div class="mt-6">
                        <button
                            class="px-6 py-3 rounded-xl font-bold text-white transition"
                            :class="answers[currentStep] === null 
                                ? 'bg-gray-300 cursor-not-allowed'
                                : 'bg-red-600 hover:bg-red-700'"
                            :disabled="answers[currentStep] === null"
                            @click="nextStep()">
                            {{ __('donate.next') }}
                        </button>
                    </div>

                </div>

            </template>

            {{-- RESULT --}}
            <template x-if="finished">

                <div>

                    {{-- ALLOWED --}}
                    <template x-if="allowed">
                        <div class="text-center">
                            <div class="text-green-600 text-6xl mb-3">✔</div>
                            <h2 class="text-2xl font-bold text-green-600">
                                {{ __('donate.congrats') }}
                            </h2>

                            <p class="text-gray-600 mt-2">
                                {{ __('donate.eligible') }}
                            </p>

                            <a href="{{ route('login') }}"
                               class="mt-6 inline-block bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                                {{ __('donate.proceed') }}
                            </a>
                        </div>
                    </template>

                    {{-- NOT ALLOWED --}}
                    <template x-if="!allowed">
                        <div class="text-center">
                            <div class="text-red-600 text-6xl mb-3">✖</div>

                            <h2 class="text-2xl font-bold text-red-600">
                                {{ __('donate.not_eligible') }}
                            </h2>

                            <p class="text-gray-600 mt-2">
                                {{ __('donate.not_allowed') }}
                            </p>

                            <p class="text-sm text-gray-400 mt-3">
                                {{ __('donate.redirect') }}
                            </p>
                        </div>
                    </template>

                </div>

            </template>

        </div>
    </div>
</div>

<script>
function donationWizard() {
    return {
        currentStep: 0,
        answers: [null, null, null, null, null],
        finished: false,
        allowed: true,

        questions: [
            { question: "{{ __('donate.questions.age') }}" },
            { question: "{{ __('donate.questions.weight') }}" },
            { question: "{{ __('donate.questions.health') }}" },
            { question: "{{ __('donate.questions.fever') }}" },
            { question: "{{ __('donate.questions.tattoo') }}" }
        ],

        nextStep() {

            // if user selected NO -> reject immediately
            if (this.answers[this.currentStep] === false) {
                this.allowed = false;
                this.finish();
                return;
            }

            // move next
            if (this.currentStep < this.questions.length - 1) {
                this.currentStep++;
            } else {
                this.allowed = true;
                this.finish();
            }
        },

        finish() {
            this.finished = true;

            if (!this.allowed) {
                setTimeout(() => {
                    window.location.href = "/";
                }, 2500);
            }
        }
    }
}
</script>