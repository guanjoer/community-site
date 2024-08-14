create table freeboard (
	id int not null auto_increment,
	author varchar(30) not null,
	password varchar(30) not null,
	title varchar(200) not null,
	content text,
	regist_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	primary key(id)
);