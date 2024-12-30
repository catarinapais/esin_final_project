# ESIN_project_MBIO.B-6 

## Project Vision and Purpose

PetPatrol: Sit and Walk is a web-based platform developed to provide a practical solution for pet care services. This platform allows users to book pet sitting and walking services or register as service providers to offer these services. The project focuses on creating a functional and user-friendly application that addresses real-world needs in the domain of pet care.

### Project Goals
- Develop a robust platform for booking and managing pet care services.
- Implement a dual-role system where users can be both clients and service providers.
- Ensure a straightforward and accessible interface suitable for diverse user groups.

This project is aimed at demonstrating the practical application of web development skills using HTML, CSS, and PHP to solve a relevant problem effectively.

---

## Setup and Docker Container Guide

This guide will help set up and run a Docker container for the `ESIN_project_MBIO.B-6` folder.


---

## Folder Structure

The project folder, `ESIN_project_MBIO.B-6`, includes the file `views/initialPage.php`, which will be the main page for the application. All other necessary files (SQL, HTML, CSS, PHP,  images and other files) are included as well.

---

## Running the Docker Container

### Windows

1. **Open PowerShell as Admin**:
   Press `Win + X` and choose "Windows PowerShell (Admin)".

2. **Go to Your C Drive**:
   ```powershell
     cd C:\
     ```

3. **Create a Folder for the Project**:
   ```powershell
     mkdir ESIN_project_MBIO.B-6
     ```

4. **Add Project Files**:
   Put all project files into the folder `C:\ESIN_project_MBIO.B-6`.

5. **Run the Docker Container**:
   ```powershell
     docker run -d -p 9000:8080 -it --name=php -v C:\ESIN_project_MBIO.B-6:/var/www/html gfcg/vesica-php73:dev
     ```

6. **View the Application**:
   Open a browser and go to `http://localhost:9000/views/initialPage.php`. This is the initial page of the project, and it should be opened like this.

### Linux / macOS

1. **Open Terminal**.

2. **Create a Project Folder**:
   ```bash
     mkdir ~/ESIN_project_MBIO.B-6
     ```

3. **Add Project Files**:
   Put all project files into the folder `~/ESIN_project_MBIO.B-6`.

4. **Run the Docker Container**:
   ```bash
     sudo docker run -d -p 9000:8080 -it --name=php -v ~/ESIN_project_MBIO.B-6:/var/www/html gfcg/vesica-php73:dev
     ```

5. **View the Application**:
   Open a browser and go to `http://localhost:9000/views/initialPage.php`. 
