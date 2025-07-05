-- 01. Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    role ENUM('student', 'coordinator', 'instructor', 'admin') NOT NULL
);

-- 02. Department table
CREATE TABLE department (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

-- 03. Admin table
CREATE TABLE admin (
    admin_id INT PRIMARY KEY, -- FK → users.user_id
    name VARCHAR(100),
    email VARCHAR(100)
);

-- 04. Student table
CREATE TABLE student (
    student_id INT PRIMARY KEY, -- FK → users.user_id
    name VARCHAR(100),
    email VARCHAR(100),
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

-- 05. Subject Coordinator table
CREATE TABLE subject_coordinator (
    coordinator_id INT PRIMARY KEY, -- FK → users.user_id
    name VARCHAR(100),
    email VARCHAR(100),
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

-- 06. Lab Instructor table
CREATE TABLE lab_instructor (
    instructor_id INT PRIMARY KEY, -- FK → users.user_id
    name VARCHAR(100),
    email VARCHAR(100),
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

-- 07. Semester table
CREATE TABLE semester (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

-- 08. Subjects table
CREATE TABLE subjects (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    course_code VARCHAR(100),
    coordinator_id INT,
    department_id INT,
    semester_id INT,
    FOREIGN KEY (coordinator_id) REFERENCES subject_coordinator(coordinator_id),
    FOREIGN KEY (department_id) REFERENCES department(department_id),
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id)
);

-- 09. Lab table
CREATE TABLE lab (
    lab_id INT PRIMARY KEY AUTO_INCREMENT,
    lab_name VARCHAR(100),
    location VARCHAR(100),
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

-- 10. Lab Schedule table
CREATE TABLE lab_schedule (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT,
    date DATE,
    time_slot VARCHAR(50),
    lab_id INT,
    instructor_id INT,
    semester_id INT,
    department_id INT,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (lab_id) REFERENCES lab(lab_id),
    FOREIGN KEY (instructor_id) REFERENCES lab_instructor(instructor_id),
    FOREIGN KEY (semester_id) REFERENCES semester(semester_id),
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

-- 11. Reschedule Request table
CREATE TABLE reschedule_request (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    subject_id INT,
    original_date DATE,
    reason TEXT,
    status ENUM('Pending', 'Approved by Admin', 'Rejected by Admin', 'Forwarded by Coordinator', 'Rejected by Coordinator', 'Scheduled') DEFAULT 'Pending',
    medical_form VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

-- 12. Attendance table
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT,
    student_id INT,
    schedule_id INT,
    status ENUM('Present', 'Absent'),
    PRIMARY KEY (attendance_id, student_id),
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (schedule_id) REFERENCES lab_schedule(schedule_id)
);
