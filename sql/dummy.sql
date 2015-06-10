INSERT INTO users (user_first_name, user_last_name, user_username, user_password) VALUES ('Test', 'Test', 'test', '864965dc2911ffaae589fc164204d1f05b5dc20d809a1954daf5e2fcc56c75fb');

INSERT INTO salts (user_id, salt) VALUES (1, 'gI');

INSERT INTO candidates (candidate_name) VALUES ('Pikachu');
INSERT INTO candidates (candidate_name) VALUES ('Muk');
INSERT INTO candidates (candidate_name) VALUES ('Ho-Oh');

INSERT INTO questions (question_id, question) VALUES (1, 'What is the middle name of your father?');
INSERT INTO questions (question_id, question) VALUES (2, 'What is the name of your elementary school?');
INSERT INTO questions (question_id, question) VALUES (3, 'What is the name of your first pet?');
INSERT INTO questions (question_id, question) VALUES (4, 'What is the birthday of your best friend?');
INSERT INTO questions (question_id, question) VALUES (5, 'What is the location of your first vacation?');
INSERT INTO questions (question_id, question) VALUES (6, 'What is the first name of the first person you had a crush on?');
INSERT INTO questions (question_id, question) VALUES (7, 'How much wood can a woodchuck chuck if a woodchuck could chuck wood?');
INSERT INTO questions (question_id, question) VALUES (8, 'What is your first favorite Pokemon?');
INSERT INTO questions (question_id, question) VALUES (9, 'What is your first favorite Pokemon attack?');
INSERT INTO questions (question_id, question) VALUES (10, 'What is the color of your first favorite shiny Pokemon?');
/**
 * Admin
 */

INSERT INTO admins (admin_username, admin_password) VALUES ('admin', '713e91d967460f8a052a50bf5ef563d909f94ce93f873b556e67d2942cf7aa5a');

INSERT INTO admin_salts (admin_id, salt) VALUES (1, 'oL');
