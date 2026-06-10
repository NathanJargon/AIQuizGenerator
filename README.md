# 📘 AI Quiz Generator

An AI-powered web application that converts PDF learning materials into structured multiple-choice quizzes using LLMs (Groq API with LLaMA 3.3 70B).

---

## 🚀 Features

* 📄 Upload PDF modules
* 🤖 AI-generated quizzes using Groq (LLaMA 3.3 70B)
* ❓ Automatic creation of 15 multiple-choice questions
* 🧠 Includes answers and explanations
* 💾 Stores quizzes per user
* 📚 View and manage generated quizzes
* 🗑 Delete unwanted quizzes
* 🔐 Authentication system (user-based access)

---

## 🏗️ Tech Stack

* **Backend:** Laravel (PHP)
* **Frontend:** Blade + Custom CSS (Modern minimal UI)
* **AI Engine:** Groq API (LLaMA 3.3 70B Versatile)
* **PDF Parser:** smalot/pdfparser
* **Database:** MySQL / MariaDB
* **Authentication:** Laravel Auth

---

## ⚙️ How It Works

1. User uploads a PDF file
2. Server stores file in `/storage/app/pdfs`
3. Text is extracted using `smalot/pdfparser`
4. Extracted content is sent to Groq API
5. AI generates:

   * 15 multiple-choice questions
   * correct answers
   * explanations
6. Quiz is saved to database
7. User can view or delete quizzes anytime

---

## 📦 Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/ai-quiz-generator.git
cd ai-quiz-generator
```

---

### 2. Install dependencies

```bash
composer install
npm install && npm run dev
```

---

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

---

### 4. Configure `.env`

```env
APP_NAME="AI Quiz Generator"
APP_ENV=local
APP_KEY=base64:your_generated_key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

GROQ_API_KEY=your_groq_api_key_here
```

---

### 5. Run migrations

```bash
php artisan migrate
```

---

### 6. Start server

```bash
php artisan serve
```

---

## 🔐 Environment Notes

* `.env` is ignored by Git (contains secrets)
* `.env.example` is safe and shared
* Always generate a new `APP_KEY` after setup

---

## 🧠 AI Model Used

This project uses:

* **Model:** `llama-3.3-70b-versatile`
* **Provider:** Groq API
* **Purpose:** Quiz generation from educational content

---

## 📁 Project Structure

```
app/
 ├── Http/Controllers
 ├── Services (PDF + Groq logic)
resources/
 ├── views (Blade UI)
storage/
 ├── app/pdfs (uploaded files)
database/
 ├── migrations
```

---

## 🖥️ UI Overview

* Minimal dark dashboard UI
* Upload panel for PDFs
* Quiz grid layout
* Smooth micro-animations
* Responsive design

---

## ⚠️ Common Issues

### ❌ "Unsupported cipher or incorrect key length"

Run:

```bash
php artisan key:generate
```

---

### ❌ Groq API errors

Check:

* API key validity
* model name correctness
* internet connection

---

## 📌 Future Improvements

* [ ] Quiz timer mode
* [ ] Difficulty levels (easy/medium/hard)
* [ ] Export quiz to PDF
* [ ] Leaderboard system
* [ ] Flashcard mode
* [ ] Streaming AI responses

---

## 👨‍💻 Author

Developed by **Nash Andrew Bondoc**

---

## 📄 License

This project is open-source and available under the MIT License.