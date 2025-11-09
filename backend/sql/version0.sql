create table if not exists users (
  name              varchar(50) not null primary key,
  password_hash     text not null,
  joined_at         timestamp not null
);

create table if not exists posts (
  id                UUID not null primary key,
  parent            UUID,
  author            varchar(50) not null,
  created_at        timestamp not null,
  content           text not null,
  foreign key (parent) references posts (id),
  foreign key (author) references users (name)
);

create table if not exists votes (
  post              UUID not null,
  user              varchar(50) not null,
  is_upvote         boolean not null,
  foreign key (post) references posts(id),
  foreign key (user) references users(name),
  primary key (post, user)
);

create table if not exists post_filters (
  name              varchar(50) not null,
  author            varchar(50) not null,
  pf_condition      text not null,
  sort_by           text not null,
  foreign key (author) references users(name),
  primary key (name, author)
);
