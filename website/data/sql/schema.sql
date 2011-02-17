CREATE TABLE category (id BIGINT AUTO_INCREMENT, name VARCHAR(50) NOT NULL UNIQUE, comment LONGTEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255), UNIQUE INDEX category_sluggable_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE charge (id BIGINT AUTO_INCREMENT, vehicle_id BIGINT NOT NULL, user_id BIGINT NOT NULL, category_id BIGINT NOT NULL, date DATE NOT NULL, kilometers DOUBLE(18, 2) NOT NULL, amount DOUBLE(18, 2) NOT NULL, comment LONGTEXT, quantity DOUBLE(18, 2), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX vehicle_id_idx (vehicle_id), INDEX category_id_idx (category_id), INDEX user_id_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE graph (id BIGINT AUTO_INCREMENT, vehicle_display VARCHAR(20) NOT NULL, user_id BIGINT NOT NULL, category_display VARCHAR(20) NOT NULL, date_from DATE, date_to DATE, kilometers_from DOUBLE(18, 2), kilometers_to DOUBLE(18, 2), range_type VARCHAR(20) NOT NULL, sha VARCHAR(40) NOT NULL UNIQUE, format VARCHAR(5) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX user_id_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE graph_category (graph_id BIGINT, category_id BIGINT, PRIMARY KEY(graph_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE graph_vehicle (graph_id BIGINT, vehicle_id BIGINT, PRIMARY KEY(graph_id, vehicle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE vehicle (id BIGINT AUTO_INCREMENT, name VARCHAR(50) NOT NULL, user_id BIGINT NOT NULL, is_archived TINYINT(1) DEFAULT '0', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, slug VARCHAR(255), UNIQUE INDEX vehicle_sluggable_idx (slug), INDEX user_id_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_forgot_password (id BIGINT AUTO_INCREMENT, user_id BIGINT NOT NULL, unique_key VARCHAR(255), expires_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX user_id_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_group (id BIGINT AUTO_INCREMENT, name VARCHAR(255) UNIQUE, description TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_group_permission (group_id BIGINT, permission_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(group_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_permission (id BIGINT AUTO_INCREMENT, name VARCHAR(255) UNIQUE, description TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_remember_key (id BIGINT AUTO_INCREMENT, user_id BIGINT, remember_key VARCHAR(32), ip_address VARCHAR(50), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX user_id_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_user (id BIGINT AUTO_INCREMENT, first_name VARCHAR(255), last_name VARCHAR(255), email_address VARCHAR(255) NOT NULL UNIQUE, username VARCHAR(128) NOT NULL UNIQUE, algorithm VARCHAR(128) DEFAULT 'sha1' NOT NULL, salt VARCHAR(128), password VARCHAR(128), is_active TINYINT(1) DEFAULT '1', is_super_admin TINYINT(1) DEFAULT '0', last_login DATETIME, validate VARCHAR(17), list_max_per_page VARCHAR(5), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX is_active_idx_idx (is_active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_user_group (user_id BIGINT, group_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
CREATE TABLE sf_guard_user_permission (user_id BIGINT, permission_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = INNODB;
ALTER TABLE charge ADD CONSTRAINT charge_vehicle_id_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicle(id) ON DELETE CASCADE;
ALTER TABLE charge ADD CONSTRAINT charge_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE charge ADD CONSTRAINT charge_category_id_category_id FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE;
ALTER TABLE graph ADD CONSTRAINT graph_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE graph_category ADD CONSTRAINT graph_category_graph_id_graph_id FOREIGN KEY (graph_id) REFERENCES graph(id) ON DELETE CASCADE;
ALTER TABLE graph_category ADD CONSTRAINT graph_category_category_id_category_id FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE;
ALTER TABLE graph_vehicle ADD CONSTRAINT graph_vehicle_vehicle_id_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicle(id) ON DELETE CASCADE;
ALTER TABLE graph_vehicle ADD CONSTRAINT graph_vehicle_graph_id_graph_id FOREIGN KEY (graph_id) REFERENCES graph(id) ON DELETE CASCADE;
ALTER TABLE vehicle ADD CONSTRAINT vehicle_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_forgot_password ADD CONSTRAINT sf_guard_forgot_password_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_group_permission ADD CONSTRAINT sf_guard_group_permission_permission_id_sf_guard_permission_id FOREIGN KEY (permission_id) REFERENCES sf_guard_permission(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_group_permission ADD CONSTRAINT sf_guard_group_permission_group_id_sf_guard_group_id FOREIGN KEY (group_id) REFERENCES sf_guard_group(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_remember_key ADD CONSTRAINT sf_guard_remember_key_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_group ADD CONSTRAINT sf_guard_user_group_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_group ADD CONSTRAINT sf_guard_user_group_group_id_sf_guard_group_id FOREIGN KEY (group_id) REFERENCES sf_guard_group(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_permission ADD CONSTRAINT sf_guard_user_permission_user_id_sf_guard_user_id FOREIGN KEY (user_id) REFERENCES sf_guard_user(id) ON DELETE CASCADE;
ALTER TABLE sf_guard_user_permission ADD CONSTRAINT sf_guard_user_permission_permission_id_sf_guard_permission_id FOREIGN KEY (permission_id) REFERENCES sf_guard_permission(id) ON DELETE CASCADE;
