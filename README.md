# Project CKCNW

Hệ thống LMS đơn giản cho quản lý lớp học, khóa học, bài tập và bài kiểm tra trắc nghiệm.

## Tính năng chính
- Giáo viên tạo lớp, khóa học, phòng học (online), bài tập và bài kiểm tra.
- Sinh viên tham gia lớp/khóa học, làm bài và xem lại kết quả.
- Chấm điểm tự động (trắc nghiệm một đáp án hoặc nhiều đáp án).
- Trang quản lý cho giáo viên: thống kê, sửa/xóa bài, xem điểm và thời gian nộp.

## Yêu cầu môi trường
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL (hoặc MariaDB)

## Cài đặt nhanh
1. Cài dependencies:
	- `composer install`
	- `npm install`
2. Tạo file cấu hình môi trường:
	- `copy .env.example .env`
	- Cập nhật thông tin DB trong `.env` (DB_DATABASE/DB_USERNAME/DB_PASSWORD)
3. Tạo app key:
	- `php artisan key:generate`
4. Chạy migrate và seed dữ liệu mẫu:
	- `php artisan migrate --seed`
5. Build frontend:
	- `npm run build`
6. Chạy server:
	- `php artisan serve`

## Tài khoản mẫu
- Giáo viên: `teacher@example.com` / `password`
- Sinh viên: `student@example.com` / `password`

## Dữ liệu mẫu
Seed tạo sẵn:
- Lớp thử nghiệm, khóa học demo, 1 phòng học.
- 1 bài tập trắc nghiệm và 1 bài kiểm tra trắc nghiệm.
- Tự động gán sinh viên vào lớp và khóa học ở trạng thái đã duyệt.

Bạn có thể đăng nhập bằng tài khoản mẫu để thử làm bài tập/bài kiểm tra ngay.

