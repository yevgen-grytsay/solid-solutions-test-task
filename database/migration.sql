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
