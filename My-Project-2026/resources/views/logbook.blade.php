<x-app-layout>
    {{-- Resolver Type Card --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Your Conflict Resolver Type
        </h3>
        <div class="text-3xl font-bold text-indigo-600 mb-4">
            {{ $latestSave->resolver_type ?? 'Not determined yet' }}
        </div>

        <div class="space-y-2">
            @foreach ([
        'Collaborator' => $gameVars['score_collaborator'] ?? 0,
        'Compromiser' => $gameVars['score_compromiser'] ?? 0,
        'Avoider' => $gameVars['score_avoider'] ?? 0,
        'Competitor' => $gameVars['score_competitor'] ?? 0,
        'Accommodator' => $gameVars['score_accommodator'] ?? 0,
    ] as $type => $score)
                <div class="flex items-center gap-3">
                    <span class="w-32 text-sm text-gray-600 dark:text-gray-400">{{ $type }}</span>
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-500 h-3 rounded-full" style="width: {{ min($score * 10, 100) }}%">
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $score }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Chapter Choices --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Your Choices
        </h3>
        <div class="space-y-3">
            @foreach ([
        'Chapter 1' => $gameVars['choice_chapter1'] ?? null,
        'Chapter 2' => $gameVars['choice_chapter2'] ?? null,
        'Chapter 3' => $gameVars['choice_chapter3'] ?? null,
        'Chapter 4' => $gameVars['choice_chapter3'] ?? null,
        'Chapter 5' => $gameVars['choice_chapter3'] ?? null,
    ] as $chapter => $choice)
                <div class="flex gap-4 border-b pb-3 dark:border-gray-700">
                    <span class="font-medium text-gray-700 dark:text-gray-300 w-24">
                        {{ $chapter }}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        {{ $choice ?? 'Not played yet' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Reflection Form --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
            My Reflection
        </h3>

        @if (session('success'))
            <div class="mb-4 text-green-600 font-medium">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('logbook.store') }}">
            @csrf
            <textarea name="reflection_text" rows="6" placeholder="What did you learn about your conflict resolution style?"
                class="w-full border rounded-lg p-3 text-gray-900 dark:text-gray-100 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500">{{ $logbook->reflection_text ?? '' }}</textarea>

            @error('reflection_text')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-3 mt-4">
                <input type="checkbox" name="private" id="private" value="1"
                    {{ $logbook->private ?? true ? 'checked' : '' }}>
                <label for="private" class="text-sm text-gray-600 dark:text-gray-400">
                    Keep my reflection private
                </label>
            </div>

            <button type="submit" class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Save Reflection
            </button>
        </form>
    </div>
</x-app-layout>
