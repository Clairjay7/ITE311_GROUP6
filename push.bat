@echo off
:: ==============================
:: AUTO PUSH SCRIPT for GITHUB
:: Project: ITE311-GROUP6 (CodeIgniter 4)
:: By: Clairjay
:: ==============================

:: Pumunta sa project folder
cd /f/xampp/htdocs/GROUP6

:: Ipakita kung nasaan ka ngayon
echo.
echo =====================================
echo  GIT AUTO PUSH STARTED!
echo  Project Path: F:\xampp\htdocs\GROUP6
echo =====================================
echo.

:: I-add lahat ng files
git add .

:: Maglagay ng default commit message na may petsa at oras
for /f "tokens=1-4 delims=/ " %%a in ("%date%") do (
    set datestr=%%a-%%b-%%c
)
set timestr=%time:~0,2%-%time:~3,2%
git commit -m "Auto commit on %datestr% at %timestr%"

:: I-push sa GitHub main branch
git push origin main

echo.
echo =====================================
echo  ✅ SUCCESS! Changes pushed to GitHub!
echo =====================================
pause
