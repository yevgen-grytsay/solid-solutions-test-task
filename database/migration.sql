USE app;

create table tree_1 (
    id int unsigned not null auto_increment primary key,
    json text
);

INSERT INTO tree_1 (id, json) VALUES (1, '{
  "auto_increment": 3,
  "root": {
    "id": 1,
    "name": "Node #1",
    "children": [
      {
        "id": 2,
        "name": "Node #2",
        "children": []
      },
      {
        "id": 3,
        "name": "Node #3",
        "children": []
      }
    ]
  }
}');

create table tree_2 (
    id int unsigned not null auto_increment primary key,
    name varchar(255) not null,
    parent_id int unsigned not null
);

insert into tree_2(id, name, parent_id)
values
    (1, 'Root node', 0),
    (2, 'Node #2', 1),
    (3, 'Node #3', 2),
    (4, 'Node #4', 2),
    (5, 'Node #5', 1)
;
