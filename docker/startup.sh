# !/bin/bash
chown -R cv:users data content media

# Initial File Creation
file=content/index.md

if [[ ! -f "$file" ]]; then
  echo '---' > content/index.md
  echo 'title: Your New CV' >> content/index.md
  echo '---' >> content/index.md
  echo 'Write Some Content!' >> content/index.md
fi

apache2-foreground
