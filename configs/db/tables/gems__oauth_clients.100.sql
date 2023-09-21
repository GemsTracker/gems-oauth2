CREATE TABLE if not exists gems__oauth_clients (
  id                          bigint unsigned not null auto_increment,
  client_id                   varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  name                        varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  secret                      varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' not null,
  redirect                    varchar(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' null,
  active                      boolean not null,
  confidential                boolean not null,

  updated                     timestamp not null default current_timestamp on update current_timestamp,
  created                     timestamp not null default current_timestamp,

   PRIMARY KEY (id),
   INDEX(client_id)
)
    ENGINE=InnoDB
    CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';
