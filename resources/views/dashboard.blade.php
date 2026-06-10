<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Quiz Generator</title>
    <style>
        :root {
            color-scheme: dark;

            --bg: #05070d;
            --panel: rgba(255, 255, 255, 0.03);
            --panel-hover: rgba(255, 255, 255, 0.05);

            --border: rgba(255, 255, 255, 0.08);

            --text: #e7eefc;
            --muted: rgba(231, 238, 252, 0.65);

            --accent: #5eead4;

            --shadow: 0 18px 50px rgba(0, 0, 0, 0.45);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, Segoe UI, sans-serif;
            color: var(--text);

            background:
                radial-gradient(circle at 20% 0%, rgba(94, 234, 212, 0.06), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(96, 165, 250, 0.05), transparent 50%),
                var(--bg);
        }

        /* Layout */
        .page {
            width: min(1080px, calc(100% - 28px));
            margin: 0 auto;
            padding: 32px 0 64px;
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        /* Success alert (soft, not loud green box) */
        .alert-success {
            background: rgba(94, 234, 212, 0.08);
            border: 1px solid rgba(94, 234, 212, 0.18);
            color: var(--text);
            padding: 12px 14px;
            border-radius: 12px;
            animation: slideDown 0.35s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Brand */
        .brand {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand strong {
            font-size: 1.05rem;
            letter-spacing: -0.02em;
        }

        .brand span,
        .muted {
            color: var(--muted);
        }

        /* Buttons */
        .btn,
        .btn-link,
        .btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 10px 14px;
            border-radius: 10px;

            font-weight: 600;
            text-decoration: none;
            cursor: pointer;

            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        /* primary button = soft accent */
        .btn,
        .btn-link {
            background: rgba(94, 234, 212, 0.10);
            border-color: rgba(94, 234, 212, 0.18);
            color: var(--text);
        }

        .btn:hover,
        .btn-link:hover {
            background: rgba(94, 234, 212, 0.16);
            transform: translateY(-1px);
        }

        /* danger */
        .btn-danger {
            background: rgba(251, 113, 133, 0.08);
            border-color: rgba(251, 113, 133, 0.18);
            color: #ffd6de;
        }

        /* File upload hidden input */
        .file-upload input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-upload {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        /* File button (cleaner) */
        .file-btn {
            padding: 10px 14px;
            border-radius: 10px;

            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            color: var(--text);

            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-btn:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-1px);
        }

        #file-name {
            font-size: 13px;
            color: var(--muted);
        }

        /* Panels (core minimal glass) */
        .hero,
        .panel,
        .upload-card,
        .side-card,
        .list-card,
        .quiz-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            box-shadow: none;
        }

        /* Hero */
        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 22px;
            padding: 26px;
        }

        @media (max-width: 900px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }

        /* Typography (more airy) */
        h1, h2, p {
            margin: 0;
        }

        h1 {
            margin-top: 12px;
            font-size: clamp(2rem, 4vw, 3.6rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .lead {
            margin-top: 12px;
            max-width: 60ch;
            color: var(--muted);
            line-height: 1.7;
        }

        /* Sections */
        .section {
            margin-top: 28px;
        }

        /* Grid */
        .grid-quiz {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        /* Cards */
        .quiz-card {
            padding: 16px;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .quiz-card:hover {
            transform: translateY(-2px);
            background: var(--panel-hover);
        }

        /* Meta */
        .quiz-meta {
            color: var(--muted);
            font-size: 12.5px;
            display: flex;
            justify-content: space-between;
        }

        /* Inputs */
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 10px;

            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border);
            color: var(--muted);
        }

        /* Notices */
        .notice {
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(94, 234, 212, 0.08);
            border: 1px solid rgba(94, 234, 212, 0.15);
        }

        .error {
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(251, 113, 133, 0.08);
            border: 1px solid rgba(251, 113, 133, 0.15);
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

        .quiz-card .action-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .quiz-card .action-row form {
            margin: 0;
        }

        .action-row a,
        .action-row button {
            min-height: 50px;
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
                <strong>AI Quiz Generator</strong>
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
                <h1>AIQGEN</h1>
                <p class="lead">
                    Upload a PDF module and turn it into a 15-question quiz. The uploaded PDF is parsed server-side, sent to Groq using lllama-3.3-70b-versatile, and saved as a quiz with 15 MCQs, answer keys, and explanations.
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
                    <li>User uploads a PDF through the form.</li>
                    <li>Server validates the file, saves it, and shows result.</li>
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