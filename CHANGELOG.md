# Nhật ký thay đổi

## 2026-05-29
- Bổ sung hiển thị điểm và thời gian nộp cho từng bài tập/bài kiểm tra ngay trong phần quản lý khóa học; cách hoạt động: mỗi bài có thể mở rộng để xem bảng điểm, dữ liệu lấy từ bảng `grades` và hiển thị theo tên sinh viên (A–Z).
- Sắp xếp danh sách sinh viên đã duyệt và chờ duyệt theo bảng chữ cái trong trang quản lý lớp; cách hoạt động: truy vấn danh sách sinh viên theo trạng thái và `orderBy('name')` trước khi render.
- Tối ưu truy vấn bằng cách eager-load điểm và thông tin sinh viên cho bài tập/bài kiểm tra để tránh tình trạng N+1; cách hoạt động: nạp sẵn quan hệ `assignments.grades.student` và `quizzes.grades.student` trong controller.

## 2026-05-28
- Thêm các route cho giáo viên chỉnh sửa, cập nhật, xóa bài tập và bài kiểm tra; cách hoạt động: truy cập đường dẫn `edit` để mở form, `PUT` để lưu, `DELETE` để xóa.
- Tạo giao diện chỉnh sửa bài tập/bài kiểm tra với dữ liệu câu hỏi đã có sẵn và hỗ trợ đổi thứ tự câu hỏi; cách hoạt động: form dựng lại câu hỏi từ dữ liệu JSON và dùng nút lên/xuống để đổi vị trí.
- Cập nhật danh sách quản lý theo khóa học để hiển thị các nút Sửa/Xóa cho từng bài; cách hoạt động: mỗi bài có link sửa và form xóa trong danh sách quản lý.
