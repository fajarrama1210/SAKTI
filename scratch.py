import os
import re

base_dir = '/home/fajar/SAKTI/resources/views/_admin'

pattern = re.compile(r'(@empty\s*<tr[^>]*>)\s*(<td[^>]*>).*?(</td>)\s*(</tr>)', re.DOTALL | re.IGNORECASE)

def replace_empty_state(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if '@empty' not in content:
        return False
        
    new_content = r'\1\n        \2\n            <div class="text-center py-5">\n                <img src="{{ asset(\'assets/img/no data.svg\') }}" alt="No Data" style="width: 150px; opacity: 0.8; margin-bottom: 15px;">\n                <h6 class="text-muted mb-0">Tidak ada data</h6>\n            </div>\n        \3\n    \4'
    
    modified_content = pattern.sub(new_content, content)
    
    if content != modified_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(modified_content)
        print(f"Updated: {filepath}")
        return True
    return False

updated_count = 0
for root, dirs, files in os.walk(base_dir):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            if replace_empty_state(filepath):
                updated_count += 1

print(f"Total updated files: {updated_count}")
