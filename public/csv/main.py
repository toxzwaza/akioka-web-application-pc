import csv

csv_file_path = 'data.csv'
with open(csv_file_path, mode='r', encoding='utf-8') as file:
    csv_reader = csv.reader(file)
    with open('output.csv', mode='w', encoding='utf-8', newline='') as output_file:
        csv_writer = csv.writer(output_file)
        for row in csv_reader:
            row = [cell.replace(',', '').strip() for cell in row]
            if len(row) > 8:
                if row[9] == '':
                    row[9] = 0
                csv_writer.writerow(row)
