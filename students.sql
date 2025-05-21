-- Create students table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert existing data
INSERT INTO students (id, name, email, password, student_id, department) VALUES
(1, 'issa', 'issa123@gmail.com', '$2y$10$348LCrvva./OGLJksHM9FOLtHF5wzlk4blTtvXlCNNqEFu20B/Ny2', '20220109', 'Cyber Science'); 