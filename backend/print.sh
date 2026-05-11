find . -path "*/vendor/*" -prune -o -name "*.php" -print | while read f; do
  echo "===== $f ====="
  cat "$f"
  echo
done >all_php.txt
