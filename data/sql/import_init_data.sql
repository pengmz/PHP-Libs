
INSERT INTO roles (id, name, title, permission) VALUES (1, 'admin', 'Administrator', 0);
INSERT INTO roles (id, name, title, permission) VALUES (2, 'member', 'Member', 0);

INSERT INTO users (id, username, password, email, role_id) VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 1);
