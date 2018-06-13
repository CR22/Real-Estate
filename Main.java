/* TeamC Group Project
 * Benjamin Carsten, William Polakowski, Carl Root
 * John Dobise, Abraham Schwartz, Maria Edwards
 * Data Structures and Algorithms
 * Dr. Todd Wolfe
 * April 15, 2018
 */

package com.company;

import java.util.*;

public class Main {

	public static void main(String[] args) {
		//StudentVersionOne
		StudentVersionOne s1 = new StudentVersionOne("John","CS");
	    s1.put(new Course("Name", 100, 4, "A", "CS"));
        s1.put(new Course("Name", 100, 3, "B+", "CS"));
        s1.put(new Course("Name", 100, 2, "C", "CS"));
        s1.put(new Course("Name", 100, 1, "D+", "CS"));
        System.out.println(s1.getGPA());
        System.out.println(s1.getCourse("Name"));
        
        //StudentVersionHT
        StudentVersionHT s2 = new StudentVersionHT("Paul","CS");
        s2.put(new Course("Name1", 101, 4, "A", "CS"));
        s2.put(new Course("Name2", 102, 3, "B+", "CS"));
        s2.put(new Course("Name3", 103, 2, "C", "CS"));
        s2.put(new Course("Name4", 104, 1, "D+", "CS"));
        System.out.println(s2.getGPA());
        System.out.println(s2.getCourse("Name1"));
        
        
        //StudentVersionPQ
        StudentVersionPQ s3 = new StudentVersionPQ("John","CS");
        StudentVersionPQ s4 = new StudentVersionPQ("Al", "Math");
        StudentVersionPQ s5 = new StudentVersionPQ("Beth", "Liberal");
        
        s3.put(new Course("Course1", 100, 4, "A", "CS"));
        s3.put(new Course("Course2", 100, 3, "B+", "CS"));
        s3.put(new Course("Course3", 100, 2, "C", "CS"));
        s3.put(new Course("Course4", 100, 1, "D+", "CS"));
        s3.put(new Course("Course5", 100, 1, "D+", "Math"));
        
        s2.put(new Course("Course6", 100, 3, "B+", "Liberal"));
        
        s3.put(new Course("Course7", 100, 4, "A", "Math"));
        
        System.out.println(s3.getGPA());
        System.out.println(s3.getCourse("Course1"));
        System.out.println("Course taken? " + s3.tookThisCourse("Course1"));
        System.out.println("Total number of credits " + s3.getCreditCount());
        System.out.println(s3.hasCompletedUpperLevelRequirements());
        System.out.println("Eligible to graduate: " + s3.eligibleToGraduate());
      
        PriorityQueue<StudentVersionPQ> student = new PriorityQueue<StudentVersionPQ>();
      
        student.add(s3);
        student.add(s4);
        student.add(s5);
     
        while (!student.isEmpty())
        	System.out.println(student.remove());
       

	}
}



