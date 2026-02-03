@echo off
chcp 65001 >nul
setlocal EnableExtensions EnableDelayedExpansion

set "OUT=dump.txt"
del "%OUT%" 2>nul

echo PROJECT ROOT: %cd% > "%OUT%"

set "EXTS=php js ts tsx css scss html py go java cs sql json yaml yml md toml ini env sh"

for %%E in (%EXTS%) do (
  for /r %%F in (*.%%E) do (
    set "P=%%~fF"
    set "SKIP="

    rem --- исключаем папки ---
    if /i not "!P:\.git\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\vendor\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\node_modules\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\venv\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\.venv\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\dist\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\build\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\target\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\.idea\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\.vscode\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\__pycache__\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\html2pdf\=!"=="!P!" set "SKIP=1"
    if /i not "!P:\storage\framework\views\=!"=="!P!" set "SKIP=1"

    if not defined SKIP (
      >>"%OUT%" echo.
      >>"%OUT%" echo ===== FILE: !P! =====
      >>"%OUT%" echo.
      type "%%F" >> "%OUT%"
    )
  )
)

echo Done: "%OUT%"
endlocal
