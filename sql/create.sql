/**
 * User Tables
 */

CREATE TABLE users (
	user_id 						SERIAL 						PRIMARY KEY,
	user_first_name 				varchar(50) 	NOT NULL,
	user_last_name 					varchar(50) 	NOT NULL,
	user_username 					varchar(50) 	NOT NULL,
	user_password 					varchar(256) 	NOT NULL
);

CREATE TABLE salts (
	user_id 						int 			NOT NULL 					REFERENCES users(user_id),
	salt 							varchar(2) 		NOT NULL
);

CREATE TABLE candidates (
	candidate_id 					SERIAL 						PRIMARY KEY,
	candidate_name 					varchar(50) 	NOT NULL
);

CREATE TABLE votes (
	user_id 						int 			NOT NULL 	PRIMARY KEY 	REFERENCES users(user_id),
	candidate_id 					int 			NOT NULL 					REFERENCES candidates(candidate_id),
	vote_timestamp 					int 			NOT NULL
);

CREATE TABLE login_attempts (
	login_attempt_id 				SERIAL 						PRIMARY KEY,
	user_id 						int 										REFERENCES users(user_id),
	login_attempt_ip 				varchar(16) 	NOT NULL,
	login_attempt_timestamp 		int 			NOT NULL,
	pass_status						boolean
);

CREATE TABLE questions (
	question_id 					int	 						PRIMARY KEY,
	question 						varchar(256) 	NOT NULL 					
);

CREATE TABLE answers (
	user_id 						int 			NOT NULL 					REFERENCES users(user_id),
	question_id						int			 	NOT NULL					REFERENCES questions(question_id),
	answer							varchar(256) 	NOT NULL
);

CREATE TABLE cutoff_date (
	cutoff_date_id					SERIAL						PRIMARY KEY,
	cutoff_datetime					int				NOT NULL
);


/**
 * Admin Tables
 */

CREATE TABLE admins (
	admin_id 						SERIAL 						PRIMARY KEY,
	admin_username 					varchar(50) 	NOT NULL,
	admin_password 					varchar(256) 	NOT NULL
);

CREATE TABLE admin_salts (
	admin_id 						int 			NOT NULL 					REFERENCES admins(admin_id),
	salt 							varchar(2) 		NOT NULL
);

CREATE TABLE admin_login_attempts (
	admin_login_attempt_id 			SERIAL 						PRIMARY KEY,
	admin_id 						int 			NOT NULL 					REFERENCES admins(admin_id),
	admin_login_attempt_ip 			varchar(16) 	NOT NULL,
	admin_login_attempt_timestamp 	int 			NOT NULL
);
