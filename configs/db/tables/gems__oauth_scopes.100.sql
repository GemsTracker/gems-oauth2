CREATE TABLE if not exists gems__oauth_scopes (
  id                          bigint unsigned not null auto_increment,

  name                        varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  description                 varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  active                      boolean not null,

  updated                     timestamp not null default current_timestamp on update current_timestamp,
  created                     timestamp not null default current_timestamp,

   PRIMARY KEY (id),
   INDEX(name)
)
    ENGINE=InnoDB
    CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
