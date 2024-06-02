@echo off

set folder_to_clean=C:\xampp\htdocs\images

echo Deleting all files in %folder_to_clean%
del /Q "%folder_to_clean%\*"

if %errorlevel% neq 0 (
    echo Failed to delete files in %folder_to_clean%
) else (
    echo Successfully deleted files in %folder_to_clean%
)

echo Deleting specific files in the BAT file's directory

set files_to_delete=("log.txt" "backup_log.txt" "backup_log_full.txt" "match.txt" "phPrayPostError.txt" "python_google_error.txt")
set folder_of_files=C:\xampp\htdocs

for %%f in %files_to_delete% do (
    if exist "%folder_of_files%\%%~f" (
        del "%folder_of_files%\%%~f"
        if %errorlevel% neq 0 (
            echo Failed to delete %folder_of_files%\%%~f
        ) else (
            echo Successfully deleted %folder_of_files%\%%~f
        )
    ) else (
        echo File %folder_of_files%\%%~f does not exist
    )
)
echo Cleanup completed
REM pause
