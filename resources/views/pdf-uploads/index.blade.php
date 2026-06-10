<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Quiz Generator</title>
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
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0 48px;
        }

        .hero {
            display: grid;
            gap: 24px;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            padding: 32px;
            border: 1px solid var(--border);
            border-radius: 28px;
            background: var(--panel);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
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
            font-size: clamp(2.4rem, 5vw, 4.8rem);
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

        .field-label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #dbe7f8;
        }

        input[type="file"] {
            width: 100%;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: #050b15;
            color: var(--muted);
        }

        button {
            margin-top: 16px;
            border: 0;
            border-radius: 16px;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #031018;
            font-weight: 800;
            cursor: pointer;
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

        .list-section {
            margin-top: 26px;
        }

        .list-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: end;
            margin-bottom: 14px;
        }

        .list-head p {
            margin-top: 6px;
            color: var(--muted);
        }

        .list-card {
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            color: #bed0e9;
            background: rgba(255, 255, 255, 0.03);
            font-weight: 700;
        }

        td { color: #e9f2ff; }

        .muted { color: var(--muted); }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(52, 211, 153, 0.28);
            background: rgba(52, 211, 153, 0.1);
            color: #c7ffe4;
            font-size: 12px;
            font-weight: 700;
        }

        .empty {
            padding: 34px 18px;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .page { width: min(100% - 20px, 1120px); padding-top: 20px; }
            .hero { padding: 20px; }
            .list-head { flex-direction: column; align-items: start; }
            th, td { padding: 14px 14px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="hero">
            <div>
                <div class="eyebrow">PDF upload for quiz generation</div>
                <h1>Upload a PDF module and prepare it for AI quiz creation.</h1>
                <p class="lead">
                    The first feature of the system accepts a PDF, stores it locally, and extracts text for the next quiz-generation step.
                </p>

                @if (session('success'))
                    <div class="notice">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pdf-uploads.store') }}" method="POST" enctype="multipart/form-data" class="upload-card">
                    @csrf
                    <label class="field-label" for="pdf_file">Upload PDF file</label>
                    <input
                        id="pdf_file"
                        type="file"
                        name="pdf_file"
                        accept="application/pdf,.pdf"
                        required
                    >

                    <button type="submit">Upload PDF</button>
                </form>
            </div>

            <aside class="side-card">
                <h2>What happens next</h2>
                <ol>
                    <li>The PDF is stored in the local storage/app/pdfs folder.</li>
                    <li>Server-side text extraction runs with smalot/pdfparser.</li>
                    <li>The saved record becomes the input for quiz generation.</li>
                </ol>
            </aside>
        </section>

        <section class="list-section">
            <div class="list-head">
                <div>
                    <h2>Uploaded PDFs</h2>
                    <p>Recent uploads are listed here for the next quiz workflow.</p>
                </div>
                <div class="muted">{{ $uploads->count() }} file(s)</div>
            </div>

            <div class="list-card">
                <table>
                    <thead>
                        <tr>
                            <th>Original name</th>
                            <th>Stored path</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($uploads as $upload)
                            <tr>
                                <td>{{ $upload->original_name }}</td>
                                <td class="muted">{{ $upload->file_path }}</td>
                                <td><span class="badge">Ready for parsing</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty">No PDFs uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
