````markdown
# 🎬 Laravel Large Video Upload System

A scalable **large video upload system (~300MB+)** using chunked uploads, background processing, and Amazon S3 storage. Includes **real-time progress tracking**, **resumable uploads**, and **automatic email notifications**.

---

## 📌 Features

- Asynchronous **chunked upload** (5MB chunks)  
- Real-time progress tracking  
- Memory-efficient **stream merging**  
- **Resumable-safe** uploads  
- Background **video processing** using Laravel Queues  
- Secure cloud storage in **Amazon S3**  
- Automatic email notifications  
- Failure handling with retries  
- Scalable folder structure: `videos/YYYY/MM/`  
- UUID-based file naming  

---

## 🛠 Tech Stack

- **Laravel 10+**  
- **PHP 8.2+**  
- **MySQL**  
- **Laravel Queue (Database driver)**  
- **Amazon S3** (via `league/flysystem-aws-s3-v3`)  
- **SMTP Mail**  
- **JavaScript / HTML / CSS** (front-end upload)  

---

## ⚙ Installation Guide

### 1️⃣ Clone Repository & Enter Directory

```bash
git clone https://github.com/imtiyaj-php-backend-developer/laravel-large-video-upload.git
cd laravel-large-video-upload
````

### 2️⃣ Install Composer Dependencies

```bash
composer install
composer require league/flysystem-aws-s3-v3
```

### 3️⃣ Setup Environment

```bash
cp .env.example .env
```

### 4️⃣ Configure Database

Update `.env` with your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

### 5️⃣ Configure Queue

```bash
# Set in .env
QUEUE_CONNECTION=database

# Create queue table and migrate
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work
```

### 6️⃣ Configure Amazon S3

Update `.env`:

```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=your-region
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### 7️⃣ Configure Mail

Update `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="Video Upload System"
```

### 8️⃣ Run Application

```bash
php artisan serve
```

Visit in your browser:

```
http://127.0.0.1:8000
```

Upload a large video and it will process in the background, send email notifications, and store securely on S3.

---

## 📂 Upload Flow

1. User selects large video (~300MB+)
2. File is split into **5MB chunks**
3. Chunks uploaded sequentially
4. Upload session stored in database
5. Background job merges chunks into a single file
6. File uploaded to **Amazon S3**
7. Local temporary files cleaned up
8. Email notification sent
9. Status updated to `completed` in DB

---

## 📊 Database Structure

Table: `upload_sessions`

| Column            | Type      | Description                               |
| ----------------- | --------- | ----------------------------------------- |
| `upload_id`       | string    | Unique ID for the upload session          |
| `file_name`       | string    | Original file name                        |
| `total_chunks`    | integer   | Total number of chunks                    |
| `uploaded_chunks` | integer   | Number of chunks successfully uploaded    |
| `s3_path`         | string    | S3 storage path of merged video           |
| `status`          | enum      | pending / processing / completed / failed |
| `created_at`      | timestamp | Record creation timestamp                 |
| `updated_at`      | timestamp | Record last update timestamp              |

---

## 🔐 Security & Scalability

* **UUID-based file naming** prevents collisions
* **Chunk validation** ensures resumable safety
* **Retry-safe jobs** for robustness
* **Background processing** offloads large merges
* **S3 storage** for scalability and reliability
* **Automatic cleanup** after upload

---

## 🧪 Tested With

* 300MB+ MP4 video
* 5MB chunk size
* 38+ chunks successfully merged
* Background job and email notifications verified

---

## 👨‍💻 Author

**Md Imtiyaj**
Email: [imtiyaj7260@gmail.com](mailto:imtiyaj7260@gmail.com)

```

---

This version is **clean, readable, and fully includes all installation commands in one place** for easy copy-paste.  

If you want, I can also **add a small GIF or screenshot example section** to show upload progress — it makes your GitHub submission look more professional.  

Do you want me to add that?
```
