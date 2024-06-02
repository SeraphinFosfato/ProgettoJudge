import os
import sys


def detect_text(path):
    from google.cloud import vision
    import io
    os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = r"Google Cloud Vision key Here.json"
    client = vision.ImageAnnotatorClient()

    with io.open(path, 'rb') as image_file:
        content = image_file.read()

    image = vision.Image(content=content)

    response = client.text_detection(image=image)
    texts = response.text_annotations
    print('Texts:')

    for text in texts:
        print('\n"{}"'.format(text.description))

        vertices = (['({},{})'.format(vertex.x, vertex.y)
                    for vertex in text.bounding_poly.vertices])

        print('bounds: {}'.format(','.join(vertices)))

    if response.error.message:
        raise Exception(
            '{}\nUlteriori informazioni sull\'errore: '
            'https://cloud.google.com/apis/design/errors'.format(
                response.error.message))
    return texts[0].description


def write_to_text_file(file_path2, content2):
    try:
        with open(file_path2, 'w') as file:
            file.write(content2)
        print("Testo scritto in: ", file_path2)
    except Exception as e:
        print("Errore:", e)


def write_to_text_file_a(file_path2, content2):
    try:
        with open(file_path2, 'a') as file:
            file.write(content2)
            file.write("\n")
        print("Testo scritto in: ", file_path2)
    except Exception as e:
        print("Errore:", e)


def first_line_of_text(text):
    while text.startswith("1") or text.startswith("7") or text.startswith("\n") or text.startswith("."):
        text = text[1:]
    lines = text.split('\n')
    for line in lines:
        while line.startswith("1") or text.startswith("7") or line.startswith("\n") or line.startswith("."):
            line = line[1:]
        line = line.replace('+', '')
        if len(line) > 3:
            return line
    return "No line"


file_path3 = "log.txt"
file_path4 = "backup_log.txt"
file_path5 = "python_google_error.txt"
file_path6 = "backup_log_full.txt"

if len(sys.argv) != 2:
    print("Usage: python pythonscript.py <file_path>")
    sys.exit(1)
file_path = sys.argv[1]

detect_string = ""

try:
    detect_string = detect_text(file_path)
except Exception as e:
    write_to_text_file_a(file_path5, str(e))


detect_string = detect_string.encode('ascii', 'ignore').decode('ascii')
detected_text = first_line_of_text(detect_string)
write_to_text_file(file_path3, str(detected_text))
write_to_text_file_a(file_path4, str(detected_text))
write_to_text_file_a(file_path6, str(detect_string))

