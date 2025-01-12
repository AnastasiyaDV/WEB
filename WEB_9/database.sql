CREATE DATABASE `lab9` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */

-- lab9.users definition
CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `full_name` text NOT NULL,
  `login` varchar(30) NOT NULL,
  `password_hash` text NOT NULL,
  `date_of_birth` date NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_unique` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE lab9.services (
	id INT auto_increment NOT NULL,
	name VARCHAR(100) NOT NULL,
	description TEXT NOT NULL,
	CONSTRAINT Services_pk PRIMARY KEY (id),
	CONSTRAINT services_unique UNIQUE KEY (name)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

-- lab9.doctors definition

CREATE TABLE `doctors` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `full_name` text NOT NULL,
  `service_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `doctors_services_FK` (`service_id`),
  CONSTRAINT `doctors_services_FK` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE lab9.bookings (
	id BIGINT auto_increment NOT NULL,
	user_id BIGINT NOT NULL,
	doctor_id BIGINT NOT NULL,
	service_id INT NOT NULL,
	`date` DATETIME NOT NULL,
	CONSTRAINT bookings_pk PRIMARY KEY (id),
	CONSTRAINT bookings_users_FK FOREIGN KEY (user_id) REFERENCES lab9.users(id) ON DELETE CASCADE,
	CONSTRAINT bookings_services_FK FOREIGN KEY (service_id) REFERENCES lab9.services(id) ON DELETE CASCADE,
	CONSTRAINT bookings_doctors_FK FOREIGN KEY (doctor_id) REFERENCES lab9.doctors(id) ON DELETE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO lab9.services (name,description) VALUES
	 ('Консультация терапевта','Индивидуальная консультация терапевта для диагностики и лечения.'),
	 ('Анализы крови','Лабораторный анализ крови для оценки состояния здоровья.'),
	 ('ЭКГ','Электрокардиограмма (ЭКГ) для проверки работы сердца.'),
	 ('УЗИ','Ультразвуковое исследование (УЗИ) для диагностики органов.'),
	 ('Консультация стоматолога','Консультация стоматолога для оценки здоровья зубов и десен.'),
	 ('Лечение зубов','Лечение зубов, включая пломбирование и устранение кариеса.');

INSERT INTO lab9.doctors (`full_name`, `service_id`) VALUES
('Иванов Иван Иванович', 1),
('Петров Петр Петрович', 1),
('Сидоров Алексей Викторович', 1),
('Смирнова Анна Сергеевна', 2),
('Кузнецова Ольга Николаевна', 2),
('Федоров Николай Васильевич', 2),
('Васильева Елена Дмитриевна', 3),
('Григорьев Андрей Евгеньевич', 3),
('Михайлова Екатерина Александровна', 3),
('Николаев Владимир Петрович', 4),
('Соколова Татьяна Владимировна', 4),
('Киселева Наталья Анатольевна', 4),
('Орлов Сергей Михайлович', 5),
('Савельева Мария Олеговна', 5),
('Романов Олег Николаевич', 5),
('Попов Александр Викторович', 6),
('Захарова Валентина Юрьевна', 6),
('Лебедев Дмитрий Константинович', 6),
('Ершова Анастасия Павловна', 1),
('Морозов Владимир Иванович', 2),
('Головина Оксана Сергеевна', 3),
('Чернов Алексей Игоревич', 4),
('Белоусов Михаил Андреевич', 5),
('Панфилова Елена Валерьевна', 6),
('Карпов Сергей Владимирович', 1),
('Ильин Дмитрий Васильевич', 2),
('Логинова Светлана Сергеевна', 3),
('Пономарев Виктор Андреевич', 4),
('Титова Инна Николаевна', 5),
('Зайцев Павел Иванович', 6);