<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Video Upload</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.card {
    width: 400px;
    padding: 30px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    color: white;
}

.card h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
}

.file-input {
    margin-bottom: 20px;
}

.file-input input {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
    border: none;
    background: rgba(255,255,255,0.2);
    color: white;
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 12px;
    background: white;
    color: #764ba2;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.progress-container {
    margin-top: 20px;
    width: 100%;
    height: 12px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #00f260, #0575e6);
    transition: width 0.3s ease;
}
</style>
</head>
<body>

<div class="card">
    <h2>Upload Video</h2>

    <div class="file-input">
        <input type="file" id="videoFile">
    </div>

    <button onclick="uploadFile()">Upload</button>

    <div class="progress-container">
        <div id="progressBar" class="progress-bar"></div>
    </div>
</div>

<script>
async function uploadFile() {
    const file = document.getElementById('videoFile').files[0];
    if (!file) {
        alert("Please select a file first.");
        return;
    }

    const chunkSize = 5 * 1024 * 1024; // 5MB
    const totalChunks = Math.ceil(file.size / chunkSize);
    const uploadId = Date.now() + '-' + file.name;

    // 1️⃣ Start upload session
    await fetch('/api/uploads/start', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            upload_id: uploadId,
            file_name: file.name,
            total_chunks: totalChunks
        })
    });

    // 2️⃣ Upload chunks one by one
    for (let chunk = 0; chunk < totalChunks; chunk++) {

        const start = chunk * chunkSize;
        const end = Math.min(file.size, start + chunkSize);
        const blob = file.slice(start, end);

        let formData = new FormData();
        formData.append('chunk', blob);
        formData.append('upload_id', uploadId);
        formData.append('chunk_index', chunk);

        await fetch('/api/uploads/chunk', {
            method: 'POST',
            body: formData
        });

        let percent = Math.floor(((chunk + 1) / totalChunks) * 100);
        document.getElementById('progressBar').style.width = percent + '%';
    }

    // 3️⃣ Finish upload
    await fetch('/api/uploads/finish', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ upload_id: uploadId })
    });

    alert("Upload Complete. Processing in background.");
}
</script>

</body>
</html>