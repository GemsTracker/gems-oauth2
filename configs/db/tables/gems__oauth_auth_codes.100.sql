CREATE TABLE if not exists gems__oauth_auth_codes (
  id                          bigint unsigned not null auto_increment,

  auth_code                   varchar(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  user_id                     bigint unsigned not null,
  client_id                   varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  scopes                      text CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  revoked                     boolean not null,
  expires_at                  datetime not null,

  updated                     timestamp not null default current_timestamp on update current_timestamp,
  created                     timestamp not null default current_timestamp,

   PRIMARY KEY (id),
   INDEX(auth_code),
   INDEX(user_id)
)
    ENGINE=InnoDB
    CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
