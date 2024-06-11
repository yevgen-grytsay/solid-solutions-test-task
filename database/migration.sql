USE app;

create table tree (
    id int unsigned not null auto_increment primary key,
    name varchar(255) not null,
    parent_id int unsigned not null
);

insert into tree(id, name, parent_id)
values
    (1, 'Root node', 0),
    (2, 'Node #2', 1),
    (3, 'Node #3', 2),
    (4, 'Node #4', 2),
    (5, 'Node #5', 1)
;
