CREATE TABLE if not exists gems__oauth_refresh_tokens (
  id                          bigint unsigned not null auto_increment,

  refresh_token               varchar(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  access_token                varchar(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  revoked                     boolean not null,
  expires_at                  datetime not null,

  updated                     timestamp not null default current_timestamp on update current_timestamp,
  created                     timestamp not null default current_timestamp,

   PRIMARY KEY (id),
   INDEX(refresh_token),
   INDEX(access_token)
)
    ENGINE=InnoDB
    CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
