<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Quiz Generator Dashboard</title>
    <style>
        :root {
            color-scheme: dark;
            --panel: rgba(10, 19, 34, 0.86);
            --border: rgba(148, 163, 184, 0.14);
            --text: #e5eefc;
            --muted: #9fb2d0;
            --accent: #4fd1c5;
            --accent-strong: #2dd4bf;
            --success: #34d399;
            --danger: #fb7185;
            --shadow: 0 28px 80px rgba(0, 0, 0, 0.38);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(79, 209, 197, 0.12), transparent 32%),
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.12), transparent 28%),
                linear-gradient(180deg, #09101c 0%, #050b15 100%);
        }

        .page {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 48px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .brand strong {
            font-size: 1.1rem;
            letter-spacing: -0.03em;
        }

        .brand span,
        .muted { color: var(--muted); }

        .action-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn,
        .btn-link,
        .btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            padding: 12px 16px;
            font-weight: 700;
            text-decoration: none;
            border: 0;
            cursor: pointer;
        }

        .btn,
        .btn-link {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #031018;
        }

        .btn-danger {
            background: rgba(251, 113, 133, 0.14);
            color: #ffdbe1;
            border: 1px solid rgba(251, 113, 133, 0.28);
        }

        /* Hide ugly default input */
        .file-upload input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-upload {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        /* Custom file button */
        .file-btn {
            display: inline-block;
            padding: 12px 22px;

            background: linear-gradient(
                135deg,
                #2563eb,
                #3b82f6
            );

            color: white;
            font-weight: 600;

            border-radius: 12px;
            cursor: pointer;

            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .file-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.45);
        }

        .file-btn:active {
            transform: scale(0.98);
        }

        /* Selected file name */
        #file-name {
            display: inline-block;
            margin-left: 12px;
            font-size: 14px;
            color: #2563eb;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .hero, .panel {
            border: 1px solid var(--border);
            border-radius: 28px;
            background: var(--panel);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
        }

        .hero {
            display: grid;
            gap: 24px;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            padding: 32px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(79, 209, 197, 0.28);
            background: rgba(79, 209, 197, 0.08);
            color: #c8fff8;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        h1, h2, p { margin: 0; }

        h1 {
            margin-top: 18px;
            font-size: clamp(2.3rem, 5vw, 4.6rem);
            line-height: 0.98;
            letter-spacing: -0.05em;
            max-width: 12ch;
        }

        .lead {
            margin-top: 18px;
            max-width: 62ch;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.75;
        }

        .notice, .error {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid transparent;
        }

        .notice {
            border-color: rgba(52, 211, 153, 0.28);
            background: rgba(52, 211, 153, 0.1);
            color: #d9ffef;
        }

        .error {
            border-color: rgba(251, 113, 133, 0.3);
            background: rgba(251, 113, 133, 0.12);
            color: #ffe4ea;
        }

        .error ul {
            margin: 0;
            padding-left: 18px;
        }

        .upload-card, .side-card, .list-card {
            border: 1px solid var(--border);
            border-radius: 24px;
            background: rgba(7, 13, 25, 0.8);
        }

        .upload-card {
            margin-top: 20px;
            padding: 20px;
        }

        input[type="file"] {
            width: 100%;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: #050b15;
            color: var(--muted);
        }

        .side-card {
            padding: 22px;
        }

        .side-card ol {
            margin: 16px 0 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.75;
        }

        .section {
            margin-top: 26px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: end;
            margin-bottom: 14px;
        }

        .grid-quiz {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .quiz-card {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.03);
        }

        .quiz-meta {
            color: var(--muted);
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .page { width: min(100% - 20px, 1180px); padding-top: 20px; }
            .hero { padding: 20px; }
            .topbar, .section-head { flex-direction: column; align-items: start; }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="topbar">
            <div class="brand">
                <strong>AI Quiz Generator Dashboard</strong>
                <span>Signed in as {{ auth()->user()->name }}</span>
            </div>
            <div class="action-row">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-danger">Logout</button>
                </form>
            </div>
        </header>

        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif


        <section class="hero">
            <div>
                <div class="eyebrow">PDF upload for quiz generation</div>
                <h1>Upload a PDF module and turn it into a 15-question quiz.</h1>
                <p class="lead">
                    The uploaded PDF is parsed server-side, sent to Groq using llama3-70b-8192, and saved as a quiz with 15 MCQs, answer keys, and explanations.
                </p>

                @if ($errors->any())
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

            <form action="{{ route('pdf-uploads.store') }}" method="POST" enctype="multipart/form-data" class="upload-card">
                @csrf

                <div class="file-upload">
                    <input
                        id="pdf_file"
                        type="file"
                        name="pdf_file"
                        accept=".pdf"
                        required
                    >

                    <label for="pdf_file" class="file-btn">
                        📄 Choose PDF
                    </label>

                    <span id="file-name">No file selected</span>
                </div>

                <button type="submit" class="btn">
                    Generate Quiz
                </button>
            </form>
            </div>

            <aside class="side-card">
                <h2>Flow</h2>
                <ol>
                    <li>The PDF is stored in storage/app/pdfs.</li>
                    <li>Text is extracted with smalot/pdfparser.</li>
                    <li>Groq returns JSON, then the quiz and 15 questions are stored.</li>
                </ol>
            </aside>
        </section>

        <section class="section">
            <div class="section-head">
                <div>
                    <h2>Your quizzes</h2>
                    <p class="muted">All quizzes generated by the current user.</p>
                </div>
                <div class="muted">{{ $quizzes->count() }} quiz(es)</div>
            </div>

            <div class="grid-quiz">
                @forelse ($quizzes as $quiz)
                    <article class="quiz-card">
                        <div class="quiz-meta">
                            <span>{{ $quiz->questions_count }} questions</span>
                            <span>{{ $quiz->created_at->format('M d, Y') }}</span>
                        </div>
                        <h3>{{ $quiz->title }}</h3>
                        <p class="muted">Source file: {{ $quiz->source_filename }}</p>
                        <div class="action-row">
                            <a class="btn-link" href="{{ route('quizzes.show', $quiz) }}">View Quiz</a>
                            <form method="POST" action="{{ route('quizzes.destroy', $quiz) }}" onsubmit="return confirm('Delete this quiz and all questions?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="quiz-card">
                        <p class="muted">No quizzes generated yet. Upload a PDF to create the first one.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('pdf_file');
        const fileNameDisplay = document.getElementById('file-name');

        if (input && fileNameDisplay) {
            input.addEventListener('change', function () {
                if (this.files.length) {
                    fileNameDisplay.textContent = '✓ ' + this.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'No file selected';
                }
            });
        }
    });
    </script>
</body>
</html>