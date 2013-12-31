/*==============================================================*/
/* Project:     MYAPP                                    */
/*==============================================================*/

/*==============================================================*/
/* Table: users                                               */
/*==============================================================*/
DROP TABLE if exists users;
CREATE TABLE users
(
   id                   int NOT NULL auto_increment,
   username             varchar(64) NOT NULL default '',
   password             varchar(64) NOT NULL default '',
   email                varchar(128) NOT NULL default '',
   description         	varchar(2000) NOT NULL default '',
   role_id              bigint(20),
   PRIMARY KEY (id),
   UNIQUE KEY email (email)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 

CREATE INDEX user_name_index ON users(username(32));
CREATE INDEX user_email_index ON users(email(32));


/*==============================================================*/
/* Table: roles                                               */
/*==============================================================*/
DROP TABLE if exists roles;
CREATE TABLE roles
(
   id                   int NOT NULL auto_increment,
   name					varchar(32) NOT NULL,
   title				varchar(200) NULL DEFAULT '',
   permission           int(11) NOT NULL DEFAULT '0',
   description			varchar(2000) NULL DEFAULT '',
   PRIMARY KEY (id),
   UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 

CREATE UNIQUE INDEX role_index ON roles(name);

