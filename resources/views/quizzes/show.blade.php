<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $quiz->title }} - AI Quiz Generator</title>
    <style>
        :root { color-scheme: dark; --panel: rgba(10, 19, 34, 0.9); --border: rgba(148, 163, 184, 0.14); --text: #e5eefc; --muted: #9fb2d0; --accent: #4fd1c5; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: var(--text); background: linear-gradient(180deg, #09101c 0%, #050b15 100%); }
        .page { width: min(1120px, calc(100% - 32px)); margin: 0 auto; padding: 30px 0 48px; }
        .card { border: 1px solid var(--border); border-radius: 28px; background: var(--panel); padding: 28px; box-shadow: 0 28px 80px rgba(0,0,0,.38); }
        .top { display:flex; justify-content:space-between; gap:16px; align-items:start; margin-bottom: 20px; }
        .muted { color: var(--muted); }
        .q { margin-top: 16px; padding: 18px; border-radius: 20px; background: rgba(255,255,255,.03); border: 1px solid var(--border); }
        .choices { margin: 12px 0 0; padding-left: 18px; color: var(--muted); }
        .answer { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border); color: #d9ffef; }
        a { color: var(--accent); text-decoration: none; font-weight: 700; }
        .actions { display:flex; gap: 12px; align-items:center; }
        .btn { display:inline-flex; padding: 12px 16px; border-radius: 14px; background: linear-gradient(135deg, var(--accent), #2dd4bf); color: #031018; text-decoration:none; font-weight: 800; border:0; }
    </style>
</head>
<body>
    <main class="page">
        <div class="card">
            <div class="top">
                <div>
                    <a href="{{ route('dashboard') }}">Back to dashboard</a>
                    <h1>{{ $quiz->title }}</h1>
                    <p class="muted">Source file: {{ $quiz->source_filename }}</p>
                </div>
                <div class="actions">
                    <form method="POST" action="{{ route('quizzes.destroy', $quiz) }}" onsubmit="return confirm('Delete this quiz and all questions?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn" type="submit">Delete Quiz</button>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="q" style="border-color: rgba(52, 211, 153, 0.28); background: rgba(52, 211, 153, 0.1);">{{ session('success') }}</div>
            @endif

            @foreach ($quiz->questions as $question)
                <article class="q">
                    <strong>{{ $question->position }}. {{ $question->question_text }}</strong>
                    <ul class="choices">
                        <li>A. {{ $question->choice_a }}</li>
                        <li>B. {{ $question->choice_b }}</li>
                        <li>C. {{ $question->choice_c }}</li>
                        <li>D. {{ $question->choice_d }}</li>
                    </ul>
                    <div class="answer">
                        Correct Answer: {{ $question->correct_answer }}<br>
                        Explanation: {{ $question->explanation }}
                    </div>
                </article>
            @endforeach
        </div>
    </main>
</body>
</html>