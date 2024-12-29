# Esin Final Project

# ESIN_project_MBIO.B-6 Setup and Docker Container Guide

This guide will help you set up and run a Docker container for the `ESIN_project_MBIO.B-6` folder. Please follow these steps to get started.


---

## Folder Structure

The project folder, `ESIN_project_MBIO.B-6`, includes the file `views/initialPage.php`, which will be the main page for the application.

---

## Running the Docker Container

### Windows

1. **Open PowerShell as Admin**:
   - Press `Win + X` and choose "Windows PowerShell (Admin)".

2. **Go to Your C Drive**:
   - ```powershell
     cd C:\
     ```

3. **Create a Folder for the Project**:
   - ```powershell
     mkdir ESIN_project_MBIO.B-6
     ```

4. **Add Project Files**:
   - Put all project files into the folder `C:\ESIN_project_MBIO.B-6`.

5. **Run the Docker Container**:
   - ```powershell
     docker run -d -p 9000:8080 -it --name=php -v C:\ESIN_project_MBIO.B-6:/var/www/html gfcg/vesica-php73:dev
     ```
   - Explanation of the command:
     - `-d`: Runs the container in the background.
     - `-p 9000:8080`: Maps the container's port 8080 to your machine's port 9000.
     - `-it`: Opens an interactive terminal.
     - `--name=php`: Names the container "php".
     - `-v C:\ESIN_project_MBIO.B-6:/var/www/html`: Links your project folder to the container.
     - `gfcg/vesica-php73:dev`: The Docker image being used.

6. **View the Application**:
   - Open a browser and go to `http://localhost:9000/views/initialPage.php`.

### Linux / macOS

1. **Open Terminal**.

2. **Create a Project Folder**:
   - ```bash
     mkdir ~/ESIN_project_MBIO.B-6
     ```

3. **Add Project Files**:
   - Put all project files into the folder `~/ESIN_project_MBIO.B-6`.

4. **Run the Docker Container**:
   - ```bash
     sudo docker run -d -p 9000:8080 -it --name=php -v ~/ESIN_project_MBIO.B-6:/var/www/html gfcg/vesica-php73:dev
     ```

5. **View the Application**:
   - Open a browser and go to `http://localhost:9000/views/initialPage.php`. 
