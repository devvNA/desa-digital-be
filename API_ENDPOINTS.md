# Rincian API Endpoint

Berdasarkan hasil pengecekan sistem saat ini (`php artisan route:list --path=api`), terdapat **Total 64 API Endpoints** yang telah berhasil dibuat.

Berikut adalah rincian semua API endpoint yang dikelompokkan berdasarkan modulnya:

## 1. Authentication & Profil

- `POST /api/login` - Login pengguna
- `POST /api/logout` - Logout pengguna
- `POST /api/register` - Registrasi pengguna baru
- `GET /api/me` - Mengambil data user yang sedang login
- `GET /api/profile` - Mengambil data profil desa
- `POST /api/profile` - Menyimpan profil desa
- `PUT /api/profile` - Memperbarui profil desa

## 2. Dashboard

- `GET /api/dashboard` - Ringkasan data dashboard admin
- `GET /api/dashboard/head-of-family` - Ringkasan data dashboard khusus kepala keluarga

## 3. User Management

- `GET /api/user` - Mengambil seluruh data pengguna
- `POST /api/user` - Menambahkan pengguna baru
- `GET /api/user/all/paginated` - Mengambil data pengguna dengan paginasi
- `GET /api/user/{user}` - Menampilkan spesifik pengguna
- `PUT|PATCH /api/user/{user}` - Mengubah data pengguna
- `DELETE /api/user/{user}` - Menghapus data pengguna

## 4. Head Of Family (Kepala Keluarga)

- `GET /api/head-of-family` - Mengambil seluruh data kepala keluarga
- `POST /api/head-of-family` - Menambahkan data kepala keluarga
- `GET /api/head-of-family/all/paginated` - Mengambil data dengan paginasi
- `GET /api/head-of-family/{head_of_family}` - Menampilkan detail
- `PUT|PATCH /api/head-of-family/{head_of_family}` - Mengubah data
- `DELETE /api/head-of-family/{head_of_family}` - Menghapus data

## 5. Family Member (Anggota Keluarga)

- `GET /api/family-member` - Mengambil data anggota keluarga
- `POST /api/family-member` - Menambahkan anggota keluarga
- `GET /api/family-member/all/paginated` - List beserta paginasi
- `GET /api/family-member/{family_member}` - Detail anggota keluarga
- `PUT|PATCH /api/family-member/{family_member}` - Mengubah anggota
- `DELETE /api/family-member/{family_member}` - Menghapus anggota

## 6. Social Assistance (Bantuan Sosial)

- `GET /api/social-assistance` - List program bantuan sosial
- `POST /api/social-assistance` - Menambah program
- `GET /api/social-assistance/all/paginated` - List dengan paginasi
- `GET /api/social-assistance/{social_assistance}` - Detail program
- `PUT|PATCH /api/social-assistance/{social_assistance}` - Edit program
- `DELETE /api/social-assistance/{social_assistance}` - Hapus program

## 7. Social Assistance Recipient (Penerima Bantuan Sosial)

- `GET /api/social-assistance-recipient` - List penerima bantuan
- `POST /api/social-assistance-recipient` - Menambah penerima
- `GET /api/social-assistance-recipient/all/paginated` - List penerima dengan paginasi
- `GET /api/social-assistance-recipient/{social_assistance_recipient}` - Detail penerima
- `PUT|PATCH /api/social-assistance-recipient/{social_assistance_recipient}` - Ubah penerima
- `DELETE /api/social-assistance-recipient/{social_assistance_recipient}` - Hapus penerima

## 8. Event (Acara)

- `GET /api/event` - List acara
- `POST /api/event` - Membuat acara
- `GET /api/event/all/paginated` - List acara dengan paginasi
- `GET /api/event/{event}` - Detail acara
- `PUT|PATCH /api/event/{event}` - Mengubah acara
- `DELETE /api/event/{event}` - Menghapus acara

## 9. Event Participant (Peserta Acara)

- `GET /api/event-participant` - List peserta acara
- `POST /api/event-participant` - Mendaftar acara
- `GET /api/event-participant/all/paginated` - List peserta dengan paginasi
- `GET /api/event-participant/{event_participant}` - Detail kepesertaan
- `PUT|PATCH /api/event-participant/{event_participant}` - Edit kepesertaan
- `DELETE /api/event-participant/{event_participant}` - Hapus peserta

## 10. Development (Pembangunan)

- `GET /api/development` - List program pembangunan
- `POST /api/development` - Membuat program pembangunan baru
- `GET /api/development/all/paginated` - List pembangunan paginasi
- `GET /api/development/{development}` - Detail pembangunan
- `PUT|PATCH /api/development/{development}` - Memperbarui pembangunan
- `DELETE /api/development/{development}` - Menghapus pembangunan

## 11. Development Applicant (Pendaftar Pekerja Pembangunan)

- `GET /api/development-applicant` - List pendaftar pekerja
- `POST /api/development-applicant` - Mendaftar sbg pekerja
- `GET /api/development-applicant/all/paginated` - List dengan paginasi
- `GET /api/development-applicant/{development_applicant}` - Detail pendaftar
- `PUT|PATCH /api/development-applicant/{development_applicant}` - Ubah pendaftar
- `DELETE /api/development-applicant/{development_applicant}` - Hapus pendaftar

## 12. Layanan Eksternal (Midtrans / Payment Gateway)

- `POST /api/midtrans/callback` - Webhook callback dari Midtrans
