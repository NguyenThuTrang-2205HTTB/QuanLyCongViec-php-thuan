Hướng dẫn cài đặt todo_app (XAMPP)

1. Giải nén todo_app.zip vào thư mục htdocs của XAMPP, ví dụ: C:\xampp\htdocs\todo_app
2. Mở XAMPP control panel, chạy Apache và MySQL.
3. Mở phpMyAdmin (http://localhost/phpmyadmin), chọn tab SQL, import file todo_app.sql (hoặc tạo database rồi import).
   - Database mặc định trong SQL là `todo_app`.
4. Nếu MySQL có mật khẩu khác, chỉnh sửa file config/db.php để phù hợp.
5. Truy cập: http://localhost/todo_app/auth/login.php và đăng nhập bằng tài khoản demo:
   - username: admin
   - password: 123456
6. Thêm / sửa / xóa công việc từ dashboard.

LƯU Ý: Mật khẩu demo đã được mã hóa bằng password_hash().
