# 🏛️ UniReserve - Sistem Peminjaman Fasilitas Kampus

[![Security: ISO 27001](https://img.shields.io/badge/Security-ISO%2027001%3A2022-brightgreen)](https://www.iso.org/standard/27001)
[![PHP: 8.x](https://img.shields.io/badge/PHP-8.x-blue)](https://www.php.net/)
[![Database: MySQL](https://img.shields.io/badge/Database-MySQL-orange)](https://www.mysql.com/)

**UniReserve** adalah platform manajemen peminjaman fasilitas kampus (ruangan dan alat) yang dirancang untuk mendigitalisasi birokrasi kampus.
**URL** https://aspel.cyou/lisaUas/landing_page.php

---

### 6. Mekanisme Session & Cookies
Keamanan sesi dikelola melalui **Cookies** dengan validasi ganda:
* **Sisi Frontend**: Mengecek keberadaan cookie di browser untuk proteksi akses UI.
* **Sisi Backend**: Melakukan hashing SHA-256 pada token cookie dan memvalidasinya ke database (`data_user` & `admins`) sebelum mengeksekusi aksi database.

---


## 🚀 Fitur Utama

### 👨‍🎓 Portal Mahasiswa
- **Katalog Real-time**: Menampilkan daftar fasilitas beserta kapasitasnya.
- **Form Pengajuan**: Input data peminjaman dengan mudah.
- **Riwayat Peminjaman**: Monitoring status pengajuan (Menunggu, Disetujui, Ditolak).
- **Edit/Batal**: Kemampuan mengubah data (CRUD) pengajuan sebelum divalidasi admin.
- **Export Data PDF**: Download dan cadangkan data peminjaman ke format PDF.

### ⚡ Panel Administrator
- **Validasi Peminjaman**: Persetujuan pengajuan dengan fitur input alasan penolakan.
- **Kelola Fasilitas**: CRUD (Create, Read, Update, Delete) kategori dan aset fasilitas kampus.
- **Export Data EXCEL**: Backup data laporan dengan mendownload data seluruh peminjaman dalam bentuk excel.

---

## 🛠️ Tech Stack
- **Backend**: PHP 8.x (Native dengan PDO Prepared Statements)
- **Database**: MySQL (Relational Database Management)
- **Frontend**: Bootstrap 5.3, Bootstrap Icons, Inter Font Family
- **Security Tools**: SHA-256 Hashing, Cookie-based Auth

---

## LINK URL DAN DEMO
- **Link URL**: https://aspel.cyou/lisaUas/landing_page.php
- **Link DEMO**: https://drive.google.com/drive/folders/1b11lQmglN7lYrfpWhbITr9oePBcVnDdy?usp=sharing

---

## Tampilan Website
- **Landing Page**:
<img width="1920" height="1080" alt="Screenshot 2026-02-03 001817" src="https://github.com/user-attachments/assets/b5971541-52e7-4876-82aa-8777ad35f71f" />
<img width="1920" height="1080" alt="Screenshot 2026-02-03 001849" src="https://github.com/user-attachments/assets/3750b8a8-c1de-46a8-8f80-f1dab3211a75" />
<img width="1920" height="1080" alt="Screenshot 2026-02-03 001932" src="https://github.com/user-attachments/assets/70e7b334-7f5c-4eec-9502-9e68b19d992d" />


- **Login & Regist Mahasiswa**:
<img width="1920" height="1080" alt="Screenshot 2026-02-03 003703" src="https://github.com/user-attachments/assets/44155884-d667-4f04-af24-7dfcb57651f0" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/da989461-3b47-4643-b90c-1a6aeab4c577" />


- **Dashboard Mahasiswa**:
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/d78cfb8a-9d70-4231-bbd7-bb6828e30f86" />
<img width="1920" height="1080" alt="Screenshot 2026-02-03 032727" src="https://github.com/user-attachments/assets/ad33797a-5be4-4ce9-b8bf-b434c2549860" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/7d9fe38d-2dfb-4582-a3fc-03f6a95e8576" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/1d54f2aa-07cd-41bb-ae8a-b9ab05e74dcf" />
<img width="1920" height="1080" alt="Screenshot 2026-02-03 032914" src="https://github.com/user-attachments/assets/dd6902bf-1adf-4313-a4ec-9d9a7a8b2283" />


- **Fitur Download PDF(Download Surat Pengajuan Yang Disetujui)**:
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/d5fadc91-a8ed-468e-9133-5602066802ae" />

- **Fitur Download EXCEL(Download Rekapitulasi Seluruh Data Peminjaman Dari Sisi Admin)**:
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/a54899d5-75de-4971-9649-ea01b0a2569e" />
<img width="331" height="179" alt="image" src="https://github.com/user-attachments/assets/b753e042-0e6c-422e-94e2-a2599c20dd14" />


- **Login Admin**:
<img width="1920" height="1080" alt="Screenshot 2026-02-03 003725" src="https://github.com/user-attachments/assets/cd9f238d-eab8-4f35-8e2d-50e9b8e1688b" />


- **Dashboard Admin**:
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/f218799e-8b07-4ba4-a36f-eed8c3b83edd" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/7108c372-4680-4a10-87f7-121ce4cfd617" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/d82a5152-a131-4472-a348-c3c845350e76" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/f505bcbb-f65e-4cc9-b011-58552bbb16a7" />

---

## 📝 Identitas Pemilik
© 2026 UniReserve - @Copyright by **23552011432_Lisa Ayu Aryanti_23CNSB_UASWEB1**


