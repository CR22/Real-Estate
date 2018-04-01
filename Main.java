package com.company;

import java.util.PriorityQueue;

public class Main {

    public static void main(String[] args) {
        StudentVersionPQ s1 = new StudentVersionPQ("John","CS");
        StudentVersionPQ s2 = new StudentVersionPQ("Al", "Math");
        StudentVersionPQ s3 = new StudentVersionPQ("Beth", "Liberal");
        
	    s1.put(new Course("Course1", 100, 4, "A", "CS"));
        s1.put(new Course("Course2", 100, 3, "B+", "CS"));
        s1.put(new Course("Course3", 100, 2, "C", "CS"));
        s1.put(new Course("Course4", 100, 1, "D+", "CS"));
        s1.put(new Course("Course5", 100, 1, "D+", "Math"));
        
        s2.put(new Course("Course6", 100, 3, "B+", "Liberal"));
        
        s3.put(new Course("Course7", 100, 4, "A", "Math"));
//        System.out.println(s1.getGPA());
//        System.out.println(s1.getCourse("Course1"));
//        System.out.println("Course taken? " + s1.tookThisCourse("Course1"));
//        System.out.println("Total number of credits " + s1.getCreditCount());
//        System.out.println(s1.hasCompletedUpperLevelRequirements());
//        System.out.println("Eligible to graduate: " + s1.eligibleToGraduate());
        
        PriorityQueue<StudentVersionPQ> student = new PriorityQueue<StudentVersionPQ>();
        
        student.add(s1);
        student.add(s2);
        student.add(s3);
       
        while (!student.isEmpty())
			System.out.println(student.remove());

    }

}