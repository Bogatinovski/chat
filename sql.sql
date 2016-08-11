create table users
(
	unique_id int(10) UNSIGNED AUTO_INCREMENT = 1000,
	first_name varchar(20) COLLATE utf8_unicode_ci not null,
	last_name varchar(20) COLLATE utf8_unicode_ci not null,
	email varchar(100) unique not null,
	hpass varchar(255) not null,
	constraint users_pk primary key(unique_id)
);

create table connections
(
	user1 int(10) UNSIGNED,
	user2 int(10) UNSIGNED,
	constraint connections_pk primary key(user1, user2),
	constraint connections_user1_fk foreign key(user1) references users(unique_id),
	constraint connections_user2_fk foreign key(user2) references users(unique_id)
);

create table messages
(
	sender int(10) UNSIGNED,
	receiver int(10) UNSIGNED,
	message_id int(5) UNSIGNED,
	time_created datetime default NOW(),
	message text(1000) not null,
	constraint messages_pk primary key(sender, receiver, message_id),
	constraint messages_sender_fk foreign key(sender) references users(unique_id),
	constraint messages_receiver_fk foreign key(receiver) references users(unique_id)
	
);
DELIMITER $$
create trigger messages_increment before insert on messages
for each row begin
    set new.message_id=(select ifnull((select max(message_id)+1 from messages where sender=new.sender and receiver=new.receiver),1));
end;$$
DELIMITER ;
