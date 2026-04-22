<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-[#5B53ED] leading-tight">
            {{ __('Your Logbook') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-8 py-8">

        {{-- Resolver Type Card --}}
        <div class="bg-[#D9D7FF] shadow-lg rounded-2xl p-8">
            <h3 class="text-xl font-semibold mb-6 text-[#5B53ED]">
                Your Conflict Resolver Type
            </h3>

            <div class="text-4xl font-bold text-[#6E68DE] mb-6">
                {{ $latestSave->resolver_type ?? 'Not determined yet' }}
            </div>

            <div class="space-y-4">
                @foreach ([
        'Collaborator' => $gameVars['score_collaborator'] ?? 0,
        'Compromiser' => $gameVars['score_compromiser'] ?? 0,
        'Avoider' => $gameVars['score_avoider'] ?? 0,
        'Competitor' => $gameVars['score_competitor'] ?? 0,
        'Peace Keeper' => $gameVars['score_peacekeeper'] ?? 0,
    ] as $type => $score)
                    <div class="flex items-center gap-4">
                        <span class="w-32 text-sm font-medium text-[#7470B8]">
                            {{ $type }}
                        </span>

                        <div class="flex-1 bg-white/60 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full bg-[#6E68DE] transition-all duration-500"
                                style="width: {{ min($score * 10, 100) }}%">
                            </div>
                        </div>

                        <span class="text-sm text-[#5B53ED] font-semibold">
                            {{ $score }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Chapter Choices --}}
        <div class="bg-white shadow-lg rounded-2xl p-8">
            <h3 class="text-xl font-semibold mb-6 text-[#5B53ED]">
                Your Choices
            </h3>

            <div class="space-y-4">
                @foreach ([
        'Chapter 1' => $gameVars['choice_chapter1'] ?? null,
        'Chapter 2' => $gameVars['choice_chapter2'] ?? null,
        'Chapter 3' => $gameVars['choice_chapter3'] ?? null,
        'Chapter 4' => $gameVars['choice_chapter4'] ?? null,
        'Chapter 5' => $gameVars['choice_chapter5'] ?? null,
    ] as $chapter => $choice)
                    <div class="flex items-start gap-4 border-b border-[#D9D7FF] pb-3">
                        <span class="w-28 font-medium text-[#6E68DE]">
                            {{ $chapter }}
                        </span>

                        <span class="text-[#7470B8]">
                            {{ $choice ?? 'Not played yet' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reflection Form --}}
        <div class="bg-[#D9D7FF] shadow-lg rounded-2xl p-8">
            <h3 class="text-xl font-semibold mb-6 text-[#5B53ED]">
                My Reflection
            </h3>

            @if (session('success'))
                <div class="mb-4 text-green-600 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('logbook.store') }}" class="space-y-4">
                @csrf

                <textarea name="reflection_text" rows="6" placeholder="What did you learn about your conflict resolution style?"
                    class="w-full rounded-xl p-4 bg-white/80 border border-[#817AFF] text-[#5B53ED] placeholder-[#827ECF] focus:ring-2 focus:ring-[#6E68DE] focus:outline-none">{{ $logbook->reflection_text ?? '' }}</textarea>

                @error('reflection_text')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="private" id="private" value="1"
                        class="rounded border-[#817AFF] text-[#6E68DE] focus:ring-[#6E68DE]"
                        {{ $logbook->private ?? true ? 'checked' : '' }}>

                    <label for="private" class="text-sm text-[#7470B8]">
                        Keep my reflection private
                    </label>
                </div>

                <button type="submit"
                    class="mt-2 px-6 py-3 bg-[#6E68DE] text-white font-semibold rounded-xl hover:bg-[#5B53ED] transition duration-200 shadow-md">
                    Save Reflection
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
