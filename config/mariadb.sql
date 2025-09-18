create database satellite;
use satellite;

create table users (
  id                UUID not null primary key,
  name              text not null,
  password_hash     text not null,
  description       text,
  joined_at         timestamp not null
);

create table posts (
  id                UUID not null primary key,
  parent            UUID,
  author            UUID not null,
  created_at        timestamp not null,
  foreign key (parent) references posts(id),
  foreign key (author) references users(id)
);

create table votes (
  post_id           UUID not null,
  user_id           UUID not null,
  is_upvote         boolean not null,
  foreign key (post_id) references posts(id),
  foreign key (user_id) references users(id),
  primary key (post_id, user_id)
);

create table post_filters (
  id                UUID not null primary key,
  author            UUID not null,
  normalized_source text not null,
  foreign key (author) references users(id)
);
