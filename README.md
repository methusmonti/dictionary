# Dictionary Programming
1. หาไฟล์ dictionary อังกฤษ >20,000 คำ ทำเป็น text file
2. เอามาสร้างไฟล์ text โดยให้ชื่อไฟล์เป็นชื่อคำศัพท์ เช่น joke --> joke.txt โดยในเนื้อหาไฟล์เป็นคำๆ นั้น (เปิดไฟล์ joke.txt เจอคำว่า joke ในไฟล์ โดยให้มีซ้ำๆ ไป 100 ครั้ง)
3. ใช้เป็นตัวอักษรตัวเล็กทั้งหมด
4. ไฟล์เหล่านี้ให้เก็บไว้ใน directory ตามตัวอักษร 2 level เช่น
--J
  |--A  = Jam, January
  |--E  = Jean, Jerry
  |--I  = Jigsaw, Jimmy
--M
  |--A  = Mai, Mammy, Man
  |--E  = Me, Mean, Meat
5. ทำ report ของ folder size ว่ามีขนาดเท่าไหร่เป็น Kbyte และมีลิสต์ของแต่ละไฟล์ด้วย นึกถึงคำสั่ง ls -l ใน unix และทำเฉพาะ level 1
6. zip ไฟล์ทีละไดเรคทอรี เป็น a.zip, b.zip,... แล้วทำ report เปรียบเทียบว่า ขนาดก่อน zip กับหลัง zip ต่างกันเป็นกี่ %
7. เอา dictionary ลงใน Database HSQL DB หรือ H2 DB โดยรันแบบ embeded mode, ใช้ JDBC ติดต่อ โดยการออกแบบให้สามารถ query เพื่อตอบคำถามเหล่านี้ได้  
7.1 มีคำกี่คำที่มีความยาว > 5 character  
7.2 มีคำกี่คำที่มีตัวอักษรซ้ำในคำมากกว่าหรือเท่ากับ 2 character  
7.3 มีคำกี่คำที่ขึ้นต้นและลงท้ายด้วยตัวอักษรเดียวกัน  
7.4 ให้สั่งอัพเดตคำที่มีทั้งหมดให้ตัวอักษรตัวแรกเป็นตัวพิมพ์ใหญ่  
8. Export คำใน database ทั้งหมดออกมาเป็น pdf file เรียงบรรทัดละคำ (ขนาดใช้เป็น A4)
9. ข้อพิเศษ ทำทั้งหมดที่ว่ามาให้สามารถรันได้เร็วขึ้นอย่างน้อยเป็น 2 เท่าของรอบแรก

## URL
Origin - https://montivory.com/demo/dictionary/  
Optimize - https://montivory.com/demo/dictionary/optimize.php  

## Optimization
Using same foreach-loop  
Reducing if/else operator  
Using str_repeat instead of for-loop  
Reducing Database connection  
