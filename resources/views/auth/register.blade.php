@extends('layouts.guest')
@section('title', __('auth.register_title'))

@section('content')
<div class="min-h-screen bg-red-50/40 flex flex-col items-center justify-center p-4 py-12">

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 w-full max-w-2xl p-8 md:p-12">

        {{-- GO HOME BUTTON --}}
        <div class="flex justify-end mb-4">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full
                      bg-red-50 text-red-600 font-semibold text-sm
                      border border-red-100
                      hover:bg-red-600 hover:text-white
                      hover:shadow-md
                      transition-all duration-200">
                {{ __('auth.back_home') }}
            </a>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">{{ __('auth.register_title') }}</h2>
            <p class="text-gray-500 mt-2">{{ __('auth.register_subtitle') }}</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- MAIN FORM WITH FULL RWANDA LOCATION LOGIC --}}
        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
              x-data="{ 
                role: '{{ old('role', 'donor') }}',
                selectedProvince: '{{ old('province', '') }}',
                selectedDistrict: '{{ old('district', '') }}',
                selectedSector: '{{ old('sector', '') }}',
                locations: {
                    'Kigali City': {
                        'Nyarugenge': ['Gitega', 'Kanyinya', 'Kigali', 'Kimisagara', 'Mageragere', 'Muhima', 'Nyakabanda', 'Nyamirambo', 'Nyarugenge', 'Rwezamenyo'],
                        'Gasabo': ['Bumbogo', 'Gatsata', 'Jali', 'Gikomero', 'Gisozi', 'Jabana', 'Kacyiru', 'Kinyinya', 'Ndera', 'Nduba', 'Rusororo', 'Rutunga', 'Kimironko', 'Remera'],
                        'Kicukiro': ['Gahanga', 'Gatenga', 'Gikondo', 'Kagarama', 'Kanombe', 'Kicukiro', 'Kigarama', 'Masaka', 'Niboye', 'Nyarugunga']
                    },
                    'Southern Province': {
                        'Nyamagabe': ['Buruhukiro', 'Cyanika', 'Gasaka', 'Gatare', 'Kaduha', 'Kamegeli', 'Kibirizi', 'Kibumbwe', 'Kitabi', 'Mbazi', 'Mugano', 'Musange', 'Musebeya', 'Mushubi', 'Nkomane', 'Tare', 'Uwinkingi'],
                        'Huye': ['Gishamvu', 'Karama', 'Kigoma', 'Kinazi', 'Maraba', 'Mbazi', 'Mukura', 'Ngoma', 'Rusatira', 'Rwaniro', 'Tumba', 'Huye'],
                        'Nyanza': ['Busasamana', 'Busoro', 'Cyabakamyi', 'Kibirizi', 'Kigoma', 'Mukingo', 'Muyira', 'Ntyazo', 'Nyagisozi', 'Rwabicuma'],
                        'Gisagara': ['Gikonko', 'Gishubi', 'Kansi', 'Kibilizi', 'Kigembe', 'Mamba', 'Muganza', 'Mugombwa', 'Mukindo', 'Musha', 'Ndora', 'Nyanza', 'Save'],
                        'Kamonyi': ['Gacurabwenge', 'Karama', 'Kayenzi', 'Kayumbu', 'Mugina', 'Musambira', 'Ngamba', 'Nyamiyaga', 'Nyarubaka', 'Rugalika', 'Rukoma', 'Runda'],
                        'Muhanga': ['Cyeza', 'Kabacuzi', 'Kibangu', 'Kiyumba', 'Muhanga', 'Mushishiro', 'Nyabinoni', 'Nyamabuye', 'Nyarusange', 'Rongi', 'Shyogwe'],
                        'Nyaruguru': ['Cyahinda', 'Busanze', 'Kibeho', 'Mata', 'Munini', 'Kivu', 'Ngera', 'Ngoma', 'Nyabimata', 'Nyagisozi', 'Muganza', 'Ruheru', 'Ruramba', 'Rusenge'],
                        'Ruhango': ['Kinazi', 'Byimana', 'Bweramana', 'Mbuye', 'Ruhango', 'Mwendo', 'Kinihira', 'Ntongwe']
                    },
                    'Eastern Province': {
                        'Nyagatare': ['Gatunda', 'Kiyombe', 'Karama', 'Karangazi', 'Katabagemu', 'Matimba', 'Mimuri', 'Mukama', 'Musheli', 'Nyagatare', 'Rukomo', 'Rwempasha', 'Tabagwe'],
                        'Gatsibo': ['Gasange', 'Gatsibo', 'Gitoki', 'Kageyo', 'Kiramuruzi', 'Kiziguro', 'Muhura', 'Murambi', 'Ngarama', 'Nyagihanga', 'Remera', 'Rugarama', 'Rwimbogo'],
                        'Kayonza': ['Gahini', 'Kabare', 'Kabarondo', 'Mukarange', 'Murama', 'Murundi', 'Mwiri', 'Ndego', 'Nyamirama', 'Rukara', 'Ruramira', 'Rwinkwavu'],
                        'Rwamagana': ['Fumbwe', 'Gahengeri', 'Gishari', 'Karenge', 'Kigabiro', 'Muhazi', 'Musha', 'Muyumbu', 'Mwulire', 'Nyakariro', 'Nzige', 'Rubona'],
                        'Bugesera': ['Gashora', 'Juru', 'Kamabuye', 'Ntarama', 'Mareba', 'Mayange', 'Musenyi', 'Mwogo', 'Ngeruka', 'Nyamata', 'Nyarugenge', 'Rahuha', 'Rweru', 'Shyara'],
                        'Ngoma': ['Gashanda', 'Jarama', 'Karembo', 'Kazo', 'Kibungo', 'Mugesera', 'Murama', 'Mutenderi', 'Remera', 'Rukira', 'Rukumberi', 'Sake', 'Zaza'],
                        'Kirehe': ['Gahara', 'Gatore', 'Kigarama', 'Kigina', 'Kirehe', 'Mahama', 'Mpanga', 'Musaza', 'Mushikiri', 'Nasho', 'Nyamugari', 'Nyarubuye']
                    },
                    'Western Province': {
                        'Rubavu': ['Bugeshi', 'Busasamana', 'Cyanzarwe', 'Gisenyi', 'Kanama', 'Kanzenze', 'Mudende', 'Nyakiliba', 'Nyamyumba', 'Nyundo', 'Rubavu', 'Rugerero'],
                        'Rusizi': ['Bugarama', 'Butare', 'Bweyeye', 'Gashonga', 'Giheke', 'Gihundwe', 'Gikundamvura', 'Gitambi', 'Kamembe', 'Muganza', 'Mururu', 'Nkanka', 'Nkombo', 'Nkungu', 'Nyakabuye', 'Nyakarenzo', 'Nzahaha', 'Rwimbogo'],
                        'Karongi': ['Bwishyura', 'Gishyita', 'Gisovu', 'Gityaza', 'Mubuga', 'Murambi', 'Murundi', 'Mutuntu', 'Rubengera', 'Rugabano', 'Ruganda', 'Rwankuba', 'Twumba'],
                        'Nyamasheke': ['Bushekeri', 'Busubi', 'Cyato', 'Gihombo', 'Kagano', 'Kanjongo', 'Karambi', 'Kirimbi', 'Macuba', 'Mahembe', 'Nyabitekeri', 'Rangiro', 'Ruharambuga', 'Shangi', 'Tyazo'],
                        'Rutsiro': ['Boneza', 'Gihango', 'Kigeyo', 'Kivumu', 'Manihira', 'Mukura', 'Mushonyi', 'Mushubati', 'Nyabirasi', 'Ruhango', 'Rusebeya'],
                        'Nyabihu': ['Bigogwe', 'Jenda', 'Jomba', 'Kabatwa', 'Karago', 'Kintobo', 'Mukamira', 'Muringa', 'Rambura', 'Rugera', 'Rurembo', 'Shyira'],
                        'Ngororero': ['Bwira', 'Gatumba', 'Hindiro', 'Kabaya', 'Kageyo', 'Kavumu', 'Matyazo', 'Muhanda', 'Muhororo', 'Ndaro', 'Ngororero', 'Nyange', 'Sovu']
                    },
                    'Northern Province': {
                        'Musanze': ['Busogo', 'Cyuve', 'Gacaca', 'Gashaki', 'Gataraga', 'Kimonyi', 'Kinigi', 'Muhoza', 'Muko', 'Musanze', 'Nkotsi', 'Nyange', 'Remera', 'Rwaza', 'Shingiro'],
                        'Burera': ['Bungwe', 'Butaro', 'Cyanika', 'Cyeru', 'Gahunga', 'Gatebe', 'Gitovu', 'Kagogo', 'Kinoni', 'Kinyababa', 'Kivuye', 'Nemba', 'Rugarama', 'Rugendabari', 'Ruhunde', 'Rusarabuge', 'Rwerere'],
                        'Gicumbi': ['Bukure', 'Bwisige', 'Byumba', 'Cyumba', 'Giti', 'Kaniga', 'Manyagiro', 'Miyove', 'Kageyo', 'Mukarange', 'Muko', 'Mutete', 'Nyamiyaga', 'Nyankenke', 'Rubaya', 'Rukomo', 'Rushaki', 'Rutare', 'Ruvune', 'Rwamiko', 'Shangasha'],
                        'Gakenke': ['Busengo', 'Coko', 'Cyabingo', 'Gakenke', 'Gashenyi', 'Janja', 'Kamubuga', 'Karambo', 'Kivuruga', 'Mataba', 'Minazi', 'Mugunga', 'Muhondo', 'Muyongwe', 'Muzo', 'Nemba', 'Ruli', 'Rusasa', 'Rushashi'],
                        'Rulindo': ['Base', 'Burega', 'Bushoki', 'Buyoga', 'Cyinzuzi', 'Cyungo', 'Kinihira', 'Kisaro', 'Masoro', 'Mbogo', 'Murambi', 'Ngoma', 'Ntarabana', 'Rukozo', 'Rusiga', 'Shyorongi', 'Tumba']
                    }
                }
              }">
            @csrf

            {{-- Names --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.first_name') }}</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.last_name') }}</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            {{-- Contact --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            {{-- ID and DOB --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.national_id') }}</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" required maxlength="16"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.dob') }}</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            {{-- Role, Blood Type (with Unknown), and Gender --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.register_as') }}</label>
                    <select name="role" x-model="role" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="donor">{{ __('auth.role_donor') }}</option>
                        <option value="doctor">{{ __('auth.role_doctor') }}</option>
                    </select>
                </div>

                <div x-show="role === 'donor'">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.blood_type') }}</label>
                    <select name="blood_type"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="unknown" {{ old('blood_type') == 'unknown' ? 'selected' : '' }}>{{ __('auth.unknown') }}</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}" {{ old('blood_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.gender') }}</label>
                    <select name="gender" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('auth.male') }}</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('auth.female') }}</option>
                    </select>
                </div>
            </div>

            {{-- Province, District, and Sector Selection (Dynamic Hierarchy) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.province') }}</label>
                    <select name="province" x-model="selectedProvince" @change="selectedDistrict = ''; selectedSector = ''" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                        <option value="">{{ __('auth.province') }}</option>
                        <template x-for="(districts, province) in locations" :key="province">
                            <option :value="province" x-text="province"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.district') }}</label>
                    <select name="district" x-model="selectedDistrict" @change="selectedSector = ''" required :disabled="!selectedProvince"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none disabled:bg-gray-50">
                        <option value="">{{ __('auth.district') }}</option>
                        <template x-if="selectedProvince">
                            <template x-for="(sectors, district) in locations[selectedProvince]" :key="district">
                                <option :value="district" x-text="district"></option>
                            </template>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.sector') }}</label>
                    <select name="sector" x-model="selectedSector" required :disabled="!selectedDistrict"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none disabled:bg-gray-50">
                        <option value="">{{ __('auth.sector') }}</option>
                        <template x-if="selectedDistrict">
                            <template x-for="sector in locations[selectedProvince][selectedDistrict]" :key="sector">
                                <option :value="sector" x-text="sector"></option>
                            </template>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Passwords --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.password') }}</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('auth.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                </div>
            </div>

            <div class="flex items-start gap-3">
                <input type="checkbox" name="terms" id="terms" required class="mt-1 accent-red-600">
                <label for="terms" class="text-xs text-gray-600 leading-relaxed">
                    {{ __('auth.terms_text') }}
                    <a href="#" class="text-red-600 hover:underline">{{ __('auth.terms_link') }}</a>
                    {{ __('auth.terms_and_confirm') }}
                </label>
            </div>

            <button type="submit"
                class="w-full bg-red-600 text-white py-4 rounded-2xl font-bold hover:bg-red-700 transition shadow-lg">
                {{ __('auth.btn_register') }}
            </button>

            <p class="text-center text-sm text-gray-600">
                {{ __('auth.has_account') }}
                <a href="{{ route('login') }}" class="text-red-600 font-bold hover:underline">
                    {{ __('auth.btn_login_link') }}
                </a>
            </p>
        </form>
    </div>
</div>
@endsection
